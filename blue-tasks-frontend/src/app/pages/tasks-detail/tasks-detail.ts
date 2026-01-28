import { Component, OnInit, ChangeDetectionStrategy, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { ReactiveFormsModule, FormBuilder, Validators, FormGroup } from '@angular/forms';
import { TasksService, Task } from '../../services/tasks';
import { finalize, switchMap, tap, catchError } from 'rxjs/operators';
import { of } from 'rxjs';

@Component({
  selector: 'app-task-detail',
  standalone: true,
  imports: [CommonModule, RouterLink, ReactiveFormsModule],
  templateUrl: './tasks-detail.html',
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class TaskDetailComponent implements OnInit {
  loading = false;
  error = '';
  task: Task | null = null;

  form!: FormGroup;

  constructor(
    private route: ActivatedRoute,
    private tasks: TasksService,
    private fb: FormBuilder,
    private cdr: ChangeDetectorRef
  ) {
    this.form = this.fb.group({
      cuerpo: ['', [Validators.required, Validators.minLength(1)]],
    });
  }

  ngOnInit(): void {
    this.route.paramMap
      .pipe(
        tap(() => {
          this.loading = true;
          this.error = '';
        }),
        switchMap((params) => {
          const id = Number(params.get('id'));
          if (!id || Number.isNaN(id)) {
            this.error = 'ID de tarea inválido';
            this.loading = false;
            this.cdr.markForCheck();
            return of(null);
          }
          return this.tasks.get(id).pipe(
            catchError((err) => {
              this.error = err?.error?.message || 'No se pudo cargar la tarea';
              return of(null);
            }),
            finalize(() => {
              this.loading = false;
              this.cdr.markForCheck();
            })
          );
        })
      )
      .subscribe((task) => {
        if (task) {
          this.task = task;
          this.cdr.markForCheck();
        }
      });
  }

  submit() {
    if (!this.task) return;
    if (this.form.invalid) return;

    const cuerpo = this.form.value.cuerpo!;
    this.tasks.addComment(this.task.id, cuerpo).subscribe({
      next: (c) => {
        this.task!.comments = [c, ...(this.task!.comments || [])];
        this.form.reset();
      },
      error: (err) => {
        this.error = err?.error?.message || 'No se pudo agregar el comentario';
      },
    });
  }
}
