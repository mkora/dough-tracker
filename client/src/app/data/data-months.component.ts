import { Component, OnInit, Inject } from '@angular/core';
import { Router, ActivatedRoute, ParamMap } from '@angular/router';

import { Observable } from 'rxjs/Observable';
import 'rxjs/add/operator/switchMap';

import { APP_CONFIG, AppConfig } from '../app-config.module';

import { DataService } from '../data.service';

@Component({
  selector: 'data-months',
  templateUrl: './data-months.component.html'
})
export class DataMonthsComponent implements OnInit {

  //private
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
      return this.service.getMonthsTableData(this.year);

    }).subscribe((data: any) => {

      this.categories = data.categories || {};
      this.tableData = data.sums.result || [];

      if (!this.tableData.length) return;
      // dont change order!
      this.chartData = this.getChartData();
      this.calculateStats();
    });

  }

  getMonths() {
    return this.config.dataShortMonths.slice();
  }

  getCellData(month, category) {
    if (this.year === this.config.dataFirstYear)
      month += this.config.dataShortMonths.indexOf(this.config.dataFirstMonthEver.slice(0, 3));

    let label;
    for (label in this.categories)
      if (this.categories[label] === category) break;

    for (let i in this.tableData) {
      let v = this.tableData[i];
      if (v._id.month === month && v._id.category === label)
        return v.sum;
    }
    return 0;

  };

  isTotalRow(category) {
    let label;
    for (label in this.categories)
      if ((label === 'total-left' || label === 'total-spent') &&
        this.categories[label] === category)
          return true;
    return false;
  }

  isAvgColumn(month) {
    return this.months[this.months.length-1] === month;
  }

  getChartData() {
    const earned = {}, spent = {}, count = {};
    this.tableData.forEach((v, i) => {
      if (v.sum > 0 ) {
        if (earned[v._id.month] === undefined) earned[v._id.month] = 0;
        earned[v._id.month] += v.sum;
      } else {
        if (spent[v._id.month] === undefined) spent[v._id.month] = 0;
        spent[v._id.month] += v.sum;
      }
      let c = v._id.category;
      if (count[c] === undefined) count[c] = {sum: 0, count: 0};
      count[c].sum += v.sum;
      count[c].count++;
    });

    let avgs = [];
    for (let c in this.categories) {
      if (c === 'income') continue;
      if (count[c] === undefined) count[c] = {sum: 0, count: 0};
      avgs.push(count[c].sum / count[c].count);
    }

    return {
      'line': [
        {data: (<any>Object).values(spent), label: 'Spent'},
        {data: (<any>Object).values(earned), label: 'Earned'}
      ],
      'polar': [ {data: avgs, label: 'Spent'}]
    };

  }

  private updateMonths() {

    if (this.year === this.config.dataCurrentYear) {
      let lastMonth = 0;
      for (let i in this.tableData) {
        let v = this.tableData[i]._id.month;
        if (lastMonth < v) lastMonth = v;
      }
      this.months = this.config.dataShortMonths.filter((v, k) =>
        lastMonth - 1 >= k);

      return lastMonth;
    }

    if (this.year === this.config.dataFirstYear) {
      const m = this.config.dataFirstMonthEver.slice(0, 3);
      this.months = this.config.dataShortMonths.filter((v, k) =>
        this.config.dataShortMonths.indexOf(m) <= k);
    }

    return this.config.dataShortMonths.length;
  }

  private calculateStats() {

    // update months if its current or the first year
    const lastMonth = this.updateMonths() + 1;

    const left = [], spent = [], count = {};
    for (let i in this.tableData) {
      let v = this.tableData[i], m = v._id.month, c = v._id.category;

      if (count[c] === undefined) count[c] = {sum: 0, count: 0};
      count[c].sum += v.sum;
      count[c].count++;

      if (v.sum < 0) {
        if (spent[m] === undefined) spent[m] = 0;
        spent[m] += v.sum;
      }

      if (left[m] === undefined) left[m] = 0;
      left[m] += v.sum;

    }

    // add average column
    for (let c in this.categories) {
      if (this.categories.hasOwnProperty(c)) {
        this.tableData.push({
          sum: count[c] ? count[c].sum / count[c].count : 0,
          _id: {
            category: c,
            month: lastMonth
          }
        });
      }
    }

    // add total rows
    for (let month in spent) {
      this.tableData.push({
        sum: spent[month],
        _id: {
          category: 'total-spent',
          month: +month
        }
      });

      this.tableData.push({
        sum: left[month],
        _id: {
          category: 'total-left',
          month: +month
        }
      });
    }

    this.months.push('Avg');
    this.categories['total-spent'] = 'Amount Spent';
    this.categories['total-left'] = 'Amount Remain';

    // calculate intersection total & avg
    this.tableData.push({
      sum: spent.reduce((soFar, v) => soFar + v, 0) / (this.months.length - 1),
      _id: {
        category: 'total-spent',
        month: lastMonth
      }
    });

    this.tableData.push({
      sum: left.reduce((soFar, v) => soFar + v, 0) / (this.months.length - 1),
      _id: {
        category: 'total-left',
        month: lastMonth
      }
    });
  }
}
