
#include <time.h>
#include <string.h>
#include <ctype.h>
#include "inout.h"


#define COVERED   0
#define OPENED    1
#define FLAGGED   2

// --- Structures ---
typedef struct {
    int rows;
    int cols;
    int mines;
    int *cells;       // -1 = mine, sinon nombre de mines adjacentes
    unsigned char *state; // COVERED / OPENED / FLAGGED
    int opened_count;
} Board;

// --- Fonctions utilitaires (inchangées) ---

int in_bounds(Board *b, int r, int c) {
    return r >= 0 && r < b->rows && c >= 0 && c < b->cols;
}

int idx(Board *b, int r, int c) {
    return r * b->cols + c;
}

void place_mines(Board *b, int avoid_r, int avoid_c) {
    int total = b->rows * b->cols;
    int placed = 0;
    while (placed < b->mines) {
        int pos = rand() % total;
        int r = pos / b->cols;
        int c = pos % b->cols;
        
        if (b->cells[idx(b,r,c)] == -1) continue;
        if (abs(r - avoid_r) <= 1 && abs(c - avoid_c) <= 1) continue; // Zone de sécurité
        
        b->cells[idx(b,r,c)] = -1;
        placed++;
    }
}

void compute_numbers(Board *b) {
    for (int r = 0; r < b->rows; ++r) {
        for (int c = 0; c < b->cols; ++c) {
            if (b->cells[idx(b,r,c)] == -1) continue;
            int count = 0;
            for (int dr = -1; dr <= 1; ++dr)
                for (int dc = -1; dc <= 1; ++dc) {
                    if (dr==0 && dc==0) continue;
                    int nr = r + dr, nc = c + dc;
                    if (in_bounds(b,nr,nc) && b->cells[idx(b,nr,nc)] == -1) count++;
                }
            b->cells[idx(b,r,c)] = count;
        }
    }
}

void print_board(Board *b, int reveal_all) {
    printf("    ");
    for (int c = 0; c < b->cols; ++c) {
        printf("%2d ", c+1);
    }
    printf("\n   +");
    for (int c = 0; c < b->cols; ++c) printf("---");
    printf("+\n");
    for (int r = 0; r < b->rows; ++r) {
        printf("%2d |", r+1);
        for (int c = 0; c < b->cols; ++c) {
            int i = idx(b,r,c);
            if (reveal_all) {
                if (b->cells[i] == -1) printf(" * ");
                else if (b->cells[i] == 0) printf("   ");
                else printf(" %d ", b->cells[i]);
            } else {
                if (b->state[i] == COVERED) printf(" . ");
                else if (b->state[i] == FLAGGED) printf(" F ");
                else if (b->state[i] == OPENED) {
                    if (b->cells[i] == -1) printf(" * ");
                    else if (b->cells[i] == 0) printf("   ");
                    else printf(" %d ", b->cells[i]);
                }
            }
        }
        printf("|\n");
    }
    printf("   +");
    for (int c = 0; c < b->cols; ++c) printf("---");
    printf("+\n");
}

void free_board(Board *b) {
    if (b->cells) free(b->cells);
    if (b->state) free(b->state);
}

void flood_fill(Board *b, int r, int c) {
    int total = b->rows * b->cols;
    int *stack = malloc(sizeof(int) * total);
    if (!stack) return; // Sécurité
    int sp = 0;
    stack[sp++] = idx(b,r,c);

    while (sp > 0) {
        int cur = stack[--sp];
        int cr = cur / b->cols;
        int cc = cur % b->cols;

        if (!in_bounds(b,cr,cc)) continue;
        if (b->state[cur] == OPENED) continue;

        b->state[cur] = OPENED;
        b->opened_count++;

        if (b->cells[cur] == 0) {
            for (int dr = -1; dr <= 1; ++dr)
                for (int dc = -1; dc <= 1; ++dc) {
                    if (dr==0 && dc==0) continue;
                    int nr = cr + dr, nc = cc + dc;
                    if (!in_bounds(b,nr,nc)) continue;
                    int ni = idx(b,nr,nc);
                    if (b->state[ni] != OPENED && b->state[ni] != FLAGGED) {
                        if (b->cells[ni] != -1) {
                            stack[sp++] = ni;
                        }
                    }
                }
        }
    }
    free(stack);
}

int check_victory(Board *b) {
    int total = b->rows * b->cols;
    return b->opened_count == (total - b->mines);
}

// --- NOUVELLES FONCTIONS DE GESTION ---

/* Fonction qui gère une partie complète.
   Elle prend en paramètres la configuration choisie.
*/
void jouer_partie(int rows, int cols, int mines) {
    Board b;
    char buf[256];

    b.rows = rows; 
    b.cols = cols; 
    b.mines = mines;
    
    // Allocation
    b.cells = calloc(rows * cols, sizeof(int));
    b.state = calloc(rows * cols, sizeof(unsigned char));

    if (!b.cells || !b.state) {
        fprintf(stderr, "Erreur : Mémoire insuffisante.\n");
        return;
    }

    // Initialisation
    for (int i = 0; i < rows * cols; ++i) {
        b.cells[i] = 0;
        b.state[i] = COVERED;
    }
    b.opened_count = 0;

    int first_move = 1;
    int game_over = 0;
    int reveal_all = 0;

    printf("\n--- DEBUT DE LA PARTIE (%dx%d, %d mines) ---\n", rows, cols, mines);

    while (!game_over) {
        print_board(&b, 0);
        printf("Mines : %d | Cases ouvertes : %d\n", b.mines, b.opened_count);
        printf("Commande (o x y = ouvrir, f x y = drapeau, q = quitter) : ");
        
        if (!fgets(buf, sizeof(buf), stdin)) break;

        char cmd;
        int x=0, y=0;
        if (sscanf(buf, " %c %d %d", &cmd, &x, &y) < 1) continue;
        cmd = tolower(cmd);

        if (cmd == 'q') {
            printf("Partie abandonnée.\n");
            break;
        }

        if (cmd != 'o' && cmd != 'f') {
            printf("Commande inconnue.\n");
            continue;
        }

        int r = x - 1, c = y - 1; // Conversion coord 1-based vers 0-based
        if (!in_bounds(&b,r,c)) {
            printf("Coordonnées hors limites.\n");
            continue;
        }
        
        int i = idx(&b,r,c);

        // Gestion Drapeau
        if (cmd == 'f') {
            if (b.state[i] == OPENED) printf("Case déjà ouverte.\n");
            else if (b.state[i] == FLAGGED) {
                b.state[i] = COVERED;
                printf("Drapeau enlevé.\n");
            } else {
                b.state[i] = FLAGGED;
                printf("Drapeau posé.\n");
            }
            continue;
        }

        // Gestion Ouvrir
        if (b.state[i] == OPENED) {
            printf("Case déjà ouverte.\n");
            continue;
        }
        if (b.state[i] == FLAGGED) {
            printf("Enlevez le drapeau d'abord.\n");
            continue;
        }

        // Premier coup : placement des mines
        if (first_move) {
            place_mines(&b, r, c);
            compute_numbers(&b);
            first_move = 0;
        }

        // Vérification Mine
        if (b.cells[i] == -1) {
            reveal_all = 1;
            print_board(&b, reveal_all);
            printf("\nBOOM ! Vous avez perdu.\n");
            game_over = 1;
        } 
        // Vérification Case vide ou chiffre
        else {
            if (b.cells[i] == 0) {
                flood_fill(&b, r, c);
            } else {
                b.state[i] = OPENED;
                b.opened_count++;
            }

            if (check_victory(&b)) {
                reveal_all = 1;
                print_board(&b, reveal_all);
                printf("\nFELICITATIONS ! Vous avez gagné !\n");
                game_over = 1;
            }
        }
    }
    
    free_board(&b);
    printf("Appuyez sur Entrée pour revenir au menu...");
    fgets(buf, sizeof(buf), stdin);
}

/* Fonction pour le menu Options.
   Utilise le passage par adresse (pointeurs) pour modifier les variables du main.
   Voir cours 4 sur les pointeurs.
   
   *taille_idx : 1 = Petit, 2 = Grand
   *diff_idx   : 1 = Normal, 2 = Difficile
*/
void gerer_options(int *taille_idx, int *diff_idx) {
    char buf[256];
    int choix = 0;

    while (choix != 3) {
        printf("\n--- OPTIONS ---\n");
        printf("Taille actuelle : %s\n", (*taille_idx == 1) ? "Petite (9x9)" : "Grande (16x16)");
        printf("Difficulté actuelle : %s\n", (*diff_idx == 1) ? "Normale" : "Difficile");
        printf("----------------\n");
        printf("1. Changer la taille\n");
        printf("2. Changer la difficulté\n");
        printf("3. Retour au menu principal\n");
        printf("Votre choix : ");

        if (fgets(buf, sizeof(buf), stdin)) {
            sscanf(buf, "%d", &choix);
        }

        if (choix == 1) {
            printf("\nChoix de la taille :\n");
            printf("1. Petite (9x9)\n");
            printf("2. Grande (16x16)\n");
            printf("Choix : ");
            if (fgets(buf, sizeof(buf), stdin)) {
                int t;
                if (sscanf(buf, "%d", &t) == 1 && (t == 1 || t == 2)) {
                    *taille_idx = t; // Modification via pointeur
                } else {
                    printf("Choix invalide.\n");
                }
            }
        } 
        else if (choix == 2) {
            printf("\nChoix de la difficulté (nombre de bombes) :\n");
            printf("1. Normale\n");
            printf("2. Difficile (Plus de bombes)\n");
            printf("Choix : ");
            if (fgets(buf, sizeof(buf), stdin)) {
                int d;
                if (sscanf(buf, "%d", &d) == 1 && (d == 1 || d == 2)) {
                    *diff_idx = d; // Modification via pointeur
                } else {
                    printf("Choix invalide.\n");
                }
            }
        }
    }
}

int main(void) {
    srand((unsigned) time(NULL));
    char buf[256];
    int choix_menu = 0;

    // Paramètres par défaut (indices)
    // Taille: 1=9x9, 2=16x16
    // Difficulté: 1=Normal, 2=Difficile
    int taille_choisie = 1; 
    int difficulte_choisie = 1; 

    // Variables réelles du jeu
    int rows, cols, mines;

    while (1) {
        // Calcul des paramètres réels avant d'afficher le menu pour info
        if (taille_choisie == 1) {
            rows = 9; cols = 9;
            // Si diff normale 10 mines, si difficile 20 mines
            mines = (difficulte_choisie == 1) ? 10 : 20;
        } else {
            rows = 16; cols = 16;
            // Si diff normale 40 mines, si difficile 80 mines
            mines = (difficulte_choisie == 1) ? 40 : 80;
        }

        printf("\n=== MENU DEMINEUR ===\n");
        printf("Config actuelle : %dx%d avec %d mines\n", rows, cols, mines);
        printf("1. Jouer\n");
        printf("2. Options (Taille / Difficulté)\n");
        printf("3. Quitter\n");
        printf("Votre choix : ");

        if (!fgets(buf, sizeof(buf), stdin)) break;
        if (sscanf(buf, "%d", &choix_menu) != 1) continue;

        switch (choix_menu) {
            case 1:
                jouer_partie(rows, cols, mines);
                break;
            case 2:
                // Passage par adresse (&) pour modifier les variables
                gerer_options(&taille_choisie, &difficulte_choisie);
                break;
            case 3:
                printf("Au revoir !\n");
                return 0;
            default:
                printf("Choix invalide.\n");
        }
    }

    return 0;
}