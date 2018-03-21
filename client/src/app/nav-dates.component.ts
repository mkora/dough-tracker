import { Component, OnInit, Inject } from '@angular/core';
import { Router, ActivatedRoute, ParamMap } from '@angular/router';

import { APP_CONFIG, AppConfig } from './app-config.module';

import 'rxjs/add/operator/switchMap';

@Component({
  selector: 'app-nav-dates',
  templateUrl: './nav-dates.component.html'
})
export class NavDatesComponent implements OnInit {

  years: number[];

  selectedYear: number;

  selectedMonth: number;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    @Inject(APP_CONFIG) private config: AppConfig
  ) {}

  ngOnInit() {
    this.route.paramMap
      .subscribe((params: ParamMap) => {
        this.selectedYear = +params.get('year');
        this.selectedMonth = +params.get('month');
        this.years = this.getYears();
      });
  }

  getYears(): number[] {
    const years = [];
    for (let i = this.config.dataCurrentYear; i >= this.config.dataFirstYear; i--) {
        const months = [];
        const currMonth = new Date().getMonth();
        this.config.dataMonths.slice()
          .forEach((v, k, self) => {
            if ((i === this.config.dataFirstYear
                && (self.indexOf(this.config.dataFirstMonthEver) > k))
              || (i === this.config.dataCurrentYear
                && (self.indexOf(self[currMonth]) < k))
            ) {
              return;
            }
            months.push({
              num: (k + 1),
              title: v
            });
        });
        years.push({
          title: i,
          months: months
        });
    }
    return years;
  }

  isYearSelected(year: number) {
    return (year === this.selectedYear);
  }

  onYearSelect(year) {
    this.selectedYear = year;
    this.selectedMonth = 0;
    this.router.navigate(['/data', year]);
  }

  isMonthSelected(year: number, month: number) {
    return (year === this.selectedYear)
      && (month === this.selectedMonth);
  }

  onMonthSelect(year: number, month: number) {
    this.selectedYear = year;
    this.selectedMonth = month;
    this.router.navigate(['/data', year, month]);
  }

  isHomeSelected() {
    return (!this.selectedYear && !this.selectedMonth);
  }

  onHomeSelect() {
    this.selectedYear = 0;
    this.selectedMonth = 0;
    this.router.navigate(['/data']);
  }
}
