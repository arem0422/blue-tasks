import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { environment } from '../../environments/environment';

export type TaskEstado = 'pendiente' | 'en_progreso' | 'finalizada';
export type TaskPrioridad = 'baja' | 'media' | 'alta';

export interface UserMini {
  id: number;
  name: string;
  email: string;
}

export interface Comment {
  id: number;
  cuerpo: string;
  user_id: number;
  task_id: number;
  created_at: string;
  user?: UserMini;
}

export interface Task {
  id: number;
  titulo: string;
  prioridad: TaskPrioridad;
  estado: TaskEstado;
  project_id: number;
  created_at: string;
  project?: any;
  comments?: Comment[];
}

export interface Paginated<T> {
  data: T[];
  current_page: number;
  per_page: number;
  last_page: number;
  total: number;
}

@Injectable({ providedIn: 'root' })
export class TasksService {
  constructor(private http: HttpClient) {}

  list(params: {
    project_id?: number;
    estado?: TaskEstado | '';
    prioridad?: TaskPrioridad | '';
    page?: number;
    per_page?: number;
  }) {
    let hp = new HttpParams();
    Object.entries(params).forEach(([k, v]) => {
      if (v !== undefined && v !== null && v !== '') hp = hp.set(k, String(v));
    });

    return this.http.get<Paginated<Task>>(`${environment.apiUrl}/tasks`, { params: hp });
  }

  get(id: number) {
    return this.http.get<Task>(`${environment.apiUrl}/tasks/${id}`);
  }

  addComment(taskId: number, cuerpo: string) {
    return this.http.post<Comment>(`${environment.apiUrl}/tasks/${taskId}/comments`, { cuerpo });
  }
}
