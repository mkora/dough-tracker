import { Component, OnInit, Inject } from '@angular/core';
import { Router, ActivatedRoute, ParamMap } from '@angular/router';

import { Observable } from 'rxjs/Observable';
import 'rxjs/add/operator/switchMap';

import { APP_CONFIG, AppConfig } from '../app-config.module';

import { DataService } from '../data.service';

@Component({
  selector: 'app-data-months',
  templateUrl: './data-months.component.html'
})
export class DataMonthsComponent implements OnInit {

  year: number;

  months: string[];

  tableData: any[];

  categories: any;

  chartData: any;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private service: DataService,
    @Inject(APP_CONFIG) private config: AppConfig
  ) {}

  ngOnInit() {
    this.route.paramMap.switchMap((params: ParamMap) => {
      this.year = +params.get('year');
      this.months = this.getMonths();
      return this.service
        .getMonthsTableData(this.year);
    }).subscribe((data: any) => {
      this.categories = data && data.categories || {};
      this.tableData = data && data.sums.result || [];
      if (this.tableData.length) {
        // dont change order!
        this.chartData = this.getChartData();
        this.calculateStats();
      }
    });
  }

  getMonths() {
    return this.config.dataShortMonths.slice();
  }

  getCellData(month, category) {
    if (this.year === this.config.dataFirstYear) {
      month += this.config.dataShortMonths
        .indexOf(this.config.dataFirstMonthEver.slice(0, 3));
    }

    const label = Object.keys(this.categories)
      .find((key) => this.categories[key] === category)
      || undefined;

    const result = this.tableData.find((val) => {
      return (val._id.month === month
        && val._id.category === label);
    });

    return result !== undefined ? result.sum : 0;
  }

  isTotalRow(category) {
    return Object.keys(this.categories)
      .find(
        (key) => this.categories[key] === category
          && (key === 'total-left' || key === 'total-spent')
      ) || undefined;
  }

  isAvgColumn(month) {
    return this.months[this.months.length - 1] === month;
  }

  getChartData() {
    const earned = {};
    const spent = {};
    const count = {};
    const avgs = [];

    this.tableData.forEach((v, i) => {
      if (v.sum > 0 ) {
        if (earned[v._id.month] === undefined) {
          earned[v._id.month] = 0;
        }
        earned[v._id.month] += v.sum;
      } else {
        if (spent[v._id.month] === undefined) {
          spent[v._id.month] = 0;
        }
        spent[v._id.month] += v.sum;
      }
      const c = v._id.category;
      if (count[c] === undefined) {
        count[c] = { sum: 0, count: 0 };
      }
      count[c].sum += v.sum;
      count[c].count++;
    });

    Object.keys(this.categories).forEach((c) => {
      if (c !== 'income') {
        if (count[c] === undefined) {
          count[c] = { sum: 0, count: 0 };
        }
        avgs.push((count[c].sum / count[c].count));
      }
    });

    return {
      'line': [
        {
          data: (<any>Object).values(spent),
          label: 'Spent'
        },
        {
          data: (<any>Object).values(earned),
          label: 'Earned'
        }
      ],
      'polar': [
        {
          data: avgs,
          label: 'Spent'
        }
      ]
    };
  }

  private updateMonths() {
    if (this.year === this.config.dataCurrentYear) {
      let lastMonth = 0;
      for (let i = 0; i < this.tableData.length; i++) {
        const v = this.tableData[i]._id.month;
        if (lastMonth < v) {
          lastMonth = v;
        }
      }
      this.months = this.config.dataShortMonths
        .filter((v, k) => (lastMonth - 1) >= k);
      return lastMonth;
    }

    if (this.year === this.config.dataFirstYear) {
      const m = this.config.dataFirstMonthEver.slice(0, 3);
      this.months = this.config.dataShortMonths
        .filter((v, k) => this.config.dataShortMonths.indexOf(m) <= k);
    }
    return this.config.dataShortMonths.length;
  }

  private calculateStats() {
    // update months if year is current or the first year
    const lastMonth = this.updateMonths() + 1;

    const left = [];
    const spent = [];
    const count = {};
    for (let i = 0; i < this.tableData.length; i++) {
      const v = this.tableData[i];
      const m = v._id.month;
      const c = v._id.category;

      if (count[c] === undefined) {
        count[c] = { sum: 0, count: 0 };
      }
      count[c].sum += v.sum;
      count[c].count++;

      if (v.sum < 0) {
        if (spent[m] === undefined) {
          spent[m] = 0;
        }
        spent[m] += v.sum;
      }

      if (left[m] === undefined) {
        left[m] = 0;
      }
      left[m] += v.sum;
    }

    Object.keys(this.categories).forEach((c) => {
      this.tableData.push({
        sum: count[c] ? (count[c].sum / count[c].count) : 0,
        _id: {
          category: c,
          month: lastMonth
        }
      });
    });

    // add total rows
    spent.forEach((val, month) => {
      this.tableData.push({
        sum: val,
        _id: {
          category: 'total-spent',
          month: +month
        }
      });
    });

    left.forEach((val, month) => {
      this.tableData.push({
        sum: val,
        _id: {
          category: 'total-left',
          month: +month
        }
      });
    });

    this.months.push('Avg');
    this.categories['total-spent'] = 'Amount Spent';
    this.categories['total-left'] = 'Amount Remain';

    // calculate intersection total & avg
    this.tableData.push({
      sum: spent.reduce((soFar, v) => (soFar + v), 0)
        / (this.months.length - 1),
      _id: {
        category: 'total-spent',
        month: lastMonth
      }
    });

    this.tableData.push({
      sum: left.reduce((soFar, v) => (soFar + v), 0)
        / (this.months.length - 1),
      _id: {
        category: 'total-left',
        month: lastMonth
      }
    });
  }
}
