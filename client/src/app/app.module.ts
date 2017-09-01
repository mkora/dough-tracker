import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { HttpModule } from '@angular/http';
import { NgPipesModule } from 'angular-pipes';
import { ChartsModule } from 'ng2-charts';

import { DataService } from './data.service';

import { AppRoutingModule } from './app-routing.module';
import { AppConfigModule } from './app-config.module';

import { AppComponent } from './app.component';
import { HomeComponent } from './home.component';
import { PageNotFoundComponent } from './page-not-found.component';

import { NavDatesComponent } from './nav-dates.component';
import { DataComponent } from './data/data.component';
import { DataYearsComponent } from './data/data-years.component';
import { DataMonthsComponent } from './data/data-months.component';
import { DataDetailsComponent } from './data/data-details.component';
import { DataItemComponent } from './data/data-item.component';
import { ChartComponent } from './chart.component';


@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    PageNotFoundComponent,
    DataComponent,
    NavDatesComponent,
    DataYearsComponent,
    DataMonthsComponent,
    DataDetailsComponent,
    DataItemComponent,
    ChartComponent
  ],
  imports: [
    BrowserModule,
    HttpModule,
    AppRoutingModule,
    AppConfigModule,
    NgPipesModule,
    ChartsModule
  ],
  providers: [DataService],
  bootstrap: [AppComponent]
})
export class AppModule { }
