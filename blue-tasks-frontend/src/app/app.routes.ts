import { Routes } from '@angular/router';
import { AuthGuard } from './core/guards/auth-guard';

import { LoginComponent } from './pages/login/login';
import { TasksComponent } from './pages/tasks/tasks';
import { TaskDetailComponent } from './pages/tasks-detail/tasks-detail';

export const routes: Routes = [
  { path: 'login', component: LoginComponent },
  { path: 'tasks', component: TasksComponent, canActivate: [AuthGuard] },
  { path: 'tasks/:id', component: TaskDetailComponent, canActivate: [AuthGuard] },
  { path: '', pathMatch: 'full', redirectTo: 'tasks' },
  { path: '**', redirectTo: 'tasks' },
];
