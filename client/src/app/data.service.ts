import { Injectable, Inject } from '@angular/core';
import { Headers, Http, Response } from '@angular/http';

import { APP_CONFIG, AppConfig } from './app-config.module';

import { Observable } from 'rxjs/Observable';
import 'rxjs/add/observable/forkJoin';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';

@Injectable()

export class DataService {

  private headers = new Headers({
    'Content-Type': 'application/json'
  });


  constructor(
    private http: Http,
    @Inject(APP_CONFIG) private config: AppConfig
  ) { }

  getYearsTableData(): Observable<any> {
    return this.getTableData('data-tableby');
  }

  getMonthsTableData(year): Observable<any> {
    return this.getTableData(`data-tableby?y=${year}`);
  }

  getDetailsTableData(year, month): Observable<any> {
    return this.getTableData(`data-details?m=${month}&y=${year}`);
  }

  private getTableData(uri) {
    return Observable.forkJoin(
      [
        this.http
          .get(`${this.config.apiEndpoint}categories`)
          .map(res => res.json()),
        this.http
          .get(`${this.config.apiEndpoint}${uri}`)
          .map(res => res.json())
      ]
    )
    .map((data: any[]) => {
      // data[0] categories data[1] sums
      if (data[0].success === true && data[1].success) {
        return {
          categories: data[0]['data'],
          sums: data[1]['data']
        };
      }

      console.error('A call returns an error:',
        data[0].msg || '' +  data[1].msg || '');
      return null;
    })
    .catch(this.handleError);
  }

  private handleError(error: any): Observable<any> {
    console.error('Server error occurred', error);
    if (error instanceof Response) {
      return Observable.throw(error.json().error || 'A server error');
    }
    return Observable.throw(error || 'A server error');
  }


}
