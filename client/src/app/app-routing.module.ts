import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { HomeComponent } from './home.component';
import { PageNotFoundComponent } from './page-not-found.component';

import { DataComponent } from './data/data.component';
import { DataYearsComponent } from './data/data-years.component';
import { DataMonthsComponent } from './data/data-months.component';
import { DataDetailsComponent } from './data/data-details.component';

const routes: Routes = [
  {
    path: 'home',
    component: HomeComponent
  },
  {
    path: '',
    redirectTo: '/home',
    pathMatch: 'full'
  },
  {
    path: 'data',
    component: DataComponent,
    children: [
      {
        path: '',
        component: DataYearsComponent,
      },
      {
        path: ':year',
        component: DataMonthsComponent
      },
      {
        path: ':year/:month',
        component: DataDetailsComponent
      }

    ]
  },
  {
    path: '**',
    component: PageNotFoundComponent
  }
];

@NgModule({
  imports: [ RouterModule.forRoot(routes) ],
  exports: [ RouterModule ]
})

export class AppRoutingModule {}
