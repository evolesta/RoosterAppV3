import { Routes } from '@angular/router';
import { LoginComponent } from './login/login.component';
import { DashboardComponent } from './secure/dashboard/dashboard.component';
import { ClockComponent } from './secure/clock/clock.component';
import { ProfileComponent } from './secure/profile/profile.component';

export const routes: Routes = [
    { path: '', component: LoginComponent },
    { 
        path: 'dashboard', 
        component: DashboardComponent,
        children: [
            { path: '', component: ClockComponent },
            { path: 'profile', component: ProfileComponent },
        ]
    },
];
