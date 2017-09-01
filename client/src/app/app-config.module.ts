import { NgModule, InjectionToken } from '@angular/core';

import { environment } from '../environments/environment';

export let APP_CONFIG = new InjectionToken<AppConfig>('app.config');

export class AppConfig {

  apiEndpoint: string;

  dataFirstYear: number;

  dataCurrentYear: number;

  dataFirstMonthEver: string;

  dataMonths: string[];

  dataShortMonths: string[];

}

export const APP_DI_CONFIG: AppConfig = {
  apiEndpoint: environment.apiEndpoint,

  dataFirstYear: 2014,

  dataCurrentYear: new Date().getFullYear(),

  dataFirstMonthEver: 'July',

  dataMonths: [
    'January',  'February', 'March',      'April',    'May',      'June',
    'July',     'August',   'September',  'October',  'November', 'December'
  ],

  dataShortMonths: [
    'Jan',  'Feb', 'Mar', 'Apr', 'May', 'Jun',
    'Jul',  'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
  ]
};

@NgModule({
  providers: [{
    provide: APP_CONFIG,
    useValue: APP_DI_CONFIG
  }]
})

export class AppConfigModule { }
