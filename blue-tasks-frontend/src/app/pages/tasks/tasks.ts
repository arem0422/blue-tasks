import { Component, OnInit, ChangeDetectionStrategy, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { TasksService, Task, Paginated, TaskEstado, TaskPrioridad } from '../../services/tasks';
import { AuthService } from '../../services/auth';
import { finalize } from 'rxjs/operators';

@Component({
  selector: 'app-tasks',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule, RouterLink],
  templateUrl: './tasks.html',
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class TasksComponent implements OnInit {
  loading = false;
  error = '';

  page = 1;
  perPage = 10;

  result: Paginated<Task> | null = null;

  estados: Array<{ label: string; value: TaskEstado | '' }> = [
    { label: 'Todos', value: '' },
    { label: 'Pendiente', value: 'pendiente' },
    { label: 'En progreso', value: 'en_progreso' },
    { label: 'Finalizada', value: 'finalizada' },
  ];

  prioridades: Array<{ label: string; value: TaskPrioridad | '' }> = [
    { label: 'Todas', value: '' },
    { label: 'Baja', value: 'baja' },
    { label: 'Media', value: 'media' },
    { label: 'Alta', value: 'alta' },
  ];

  filters!: FormGroup;

  constructor(
    private fb: FormBuilder,
    private tasks: TasksService,
    private auth: AuthService,
    private cdr: ChangeDetectorRef
  ) {
    this.filters = this.fb.group({
      project_id: [''], // opcional por ahora
      estado: ['' as TaskEstado | ''],
      prioridad: ['' as TaskPrioridad | ''],
    });
  }

  ngOnInit(): void {
    this.load();
  }

  load(page = this.page) {
    this.page = page;
    this.loading = true;
    this.error = '';

    const f = this.filters.value;

    this.tasks.list({
      project_id: f.project_id ? Number(f.project_id) : undefined,
      estado: (f.estado ?? '') as any,
      prioridad: (f.prioridad ?? '') as any,
      page: this.page,
      per_page: this.perPage,
    })
      .pipe(finalize(() => {
        this.loading = false;
        this.cdr.markForCheck();
      }))
      .subscribe({
        next: (res) => {
          this.result = res;
          this.cdr.markForCheck();
        },
        error: (err) => {
          this.error = err?.error?.message || 'No se pudieron cargar las tareas';
          this.cdr.markForCheck();
        },
      });
  }

  applyFilters() {
    this.load(1);
  }

  prev() {
    if (!this.result) return;
    if (this.result.current_page <= 1) return;
    this.load(this.result.current_page - 1);
  }

  next() {
    if (!this.result) return;
    if (this.result.current_page >= this.result.last_page) return;
    this.load(this.result.current_page + 1);
  }

  logout() {
    this.auth.logout();
    location.href = '/login';
  }
}
