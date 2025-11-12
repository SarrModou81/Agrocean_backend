import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { MatDialog } from '@angular/material/dialog';
import { ToastrService } from 'ngx-toastr';
import { ClientsService } from '../../../core/services/clients.service';
import { Client } from '../../../core/models';
import { ConfirmDialogComponent } from '../../../shared/components/confirm-dialog/confirm-dialog.component';

@Component({
  selector: 'app-liste',
  templateUrl: './liste.component.html',
  styleUrl: './liste.component.scss'
})
export class ListeComponent implements OnInit {
  displayedColumns: string[] = ['nom', 'email', 'telephone', 'type', 'solde', 'actions'];
  clients: Client[] = [];
  loading = false;
  searchTerm = '';
  totalItems = 0;
  pageSize = 10;
  currentPage = 1;

  constructor(
    private clientsService: ClientsService,
    private router: Router,
    private dialog: MatDialog,
    private toastr: ToastrService
  ) {}

  ngOnInit(): void {
    this.loadClients();
  }

  loadClients(): void {
    this.loading = true;
    const params = {
      page: this.currentPage,
      per_page: this.pageSize,
      search: this.searchTerm || undefined
    };

    this.clientsService.getAll(params).subscribe({
      next: (response) => {
        this.clients = response.data;
        this.totalItems = response.total;
        this.loading = false;
      },
      error: (err) => {
        this.toastr.error('Erreur lors du chargement des clients', 'Erreur');
        this.loading = false;
      }
    });
  }

  onSearch(): void {
    this.currentPage = 1;
    this.loadClients();
  }

  onPageChange(event: any): void {
    this.currentPage = event.pageIndex + 1;
    this.pageSize = event.pageSize;
    this.loadClients();
  }

  onEdit(client: Client): void {
    this.router.navigate(['/clients/edit', client.id]);
  }

  onDelete(client: Client): void {
    const dialogRef = this.dialog.open(ConfirmDialogComponent, {
      width: '400px',
      data: {
        title: 'Confirmer la suppression',
        message: `Êtes-vous sûr de vouloir supprimer le client "${client.nom}" ?`,
        confirmText: 'Supprimer',
        cancelText: 'Annuler'
      }
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.clientsService.delete(client.id).subscribe({
          next: () => {
            this.toastr.success('Client supprimé avec succès');
            this.loadClients();
          },
          error: (err) => {
            this.toastr.error('Erreur lors de la suppression', 'Erreur');
          }
        });
      }
    });
  }

  onCreate(): void {
    this.router.navigate(['/clients/nouveau']);
  }
}
