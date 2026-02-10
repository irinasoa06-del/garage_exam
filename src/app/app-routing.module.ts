import { NgModule } from '@angular/core';
import { PreloadAllModules, RouterModule, Routes } from '@angular/router';
import { AuthGuard } from './core/guards/auth.guard';
import { GuestGuard } from './core/guards/guest.guard';

const routes: Routes = [
  {
    path: '',
    redirectTo: 'login',
    pathMatch: 'full'
  },
  {
    path: 'login',
    canActivate: [GuestGuard],
    loadChildren: () => import('./pages/auth/login/login.module').then(m => m.LoginPageModule)
  },
  {
    path: 'register',
    canActivate: [GuestGuard],
    loadChildren: () => import('./pages/auth/register/register.module').then(m => m.RegisterPageModule)
  },
  {
    path: 'cars',
    canActivate: [AuthGuard],
    loadChildren: () => import('./pages/cars/car-list/car-list.module').then(m => m.CarListPageModule)
  },
  {
    path: 'cars/add',
    canActivate: [AuthGuard],
    loadChildren: () => import('./pages/cars/car-add/car-add.module').then(m => m.CarAddPageModule)
  },
  {
    path: 'cars/:id',
    canActivate: [AuthGuard],
    loadChildren: () => import('./pages/cars/car-detail/car-detail.module').then(m => m.CarDetailPageModule)
  },
  {
    path: 'repairs',
    canActivate: [AuthGuard],
    loadChildren: () => import('./pages/repairs/repair-list/repair-list.module').then(m => m.RepairListPageModule)
  },
  {
    path: 'repairs/payment/:id',
    canActivate: [AuthGuard],
    loadChildren: () => import('./pages/repairs/repair-payment/repair-payment.module').then(m => m.RepairPaymentPageModule)
  },
  {
    path: 'repairs/:id',
    canActivate: [AuthGuard],
    loadChildren: () => import('./pages/repairs/repair-detail/repair-detail.module').then(m => m.RepairDetailPageModule)
  },
  {
    path: 'profile',
    canActivate: [AuthGuard],
    loadChildren: () => import('./pages/profile/profile.module').then(m => m.ProfilePageModule)
  },
  {
    path: 'notifications',
    canActivate: [AuthGuard],
    loadChildren: () => import('./pages/notifications/notifications.module').then(m => m.NotificationsPageModule)
  }
];

@NgModule({
  imports: [
    RouterModule.forRoot(routes, { preloadingStrategy: PreloadAllModules })
  ],
  exports: [RouterModule]
})
export class AppRoutingModule {}