import { Component, OnInit, Inject } from '@angular/core';
import { Router, ActivatedRoute, ParamMap } from '@angular/router';

import { APP_CONFIG, AppConfig } from './app-config.module';

import 'rxjs/add/operator/switchMap';

@Component({
  selector: 'nav-dates',
  templateUrl: './nav-dates.component.html'
})

export class NavDatesComponent {

  years: number[];

  selectedYear: number;

  selectedMonth: number;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    @Inject(APP_CONFIG) private config: AppConfig
  ) {}


  ngOnInit(): void {
    this.route.paramMap
      .subscribe((params: ParamMap) => {
        this.selectedYear = +params.get('year');
        this.selectedMonth = +params.get('month');
        this.years = this.getYears();
      });
  }

  getYears(): number[] {
    let years = [];
    for (let i = this.config.dataCurrentYear; i >= this.config.dataFirstYear; i--) {
        let months = this.config.dataMonths.slice();
        if (i == this.config.dataFirstYear) {
          months = months.filter((v, k) =>
            months.indexOf(this.config.dataFirstMonthEver) <= k);
        }
        if (i == this.config.dataCurrentYear) {
          months = months.filter((v, k) =>
            months.indexOf(this.config.dataFirstMonthEver) >= k);
        }
        years.push({
          title: i,
          months: months
        });
    }
    return years;
  }

  isYearSelected(year: number) {
    return year === this.selectedYear;
  }

  onYearSelect(year) {
    this.selectedYear = year;
    this.selectedMonth = 0;
    this.router.navigate(['/data', year]);
  }

  isMonthSelected(year: number, month: number) {
    return year === this.selectedYear && month === this.selectedMonth;
  }

  onMonthSelect(year:number, month: number) {
    this.selectedYear = year;
    this.selectedMonth = month;
    this.router.navigate(['/data', year, month]);
  }


}
