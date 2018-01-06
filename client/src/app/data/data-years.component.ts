import { Component, OnInit, Inject } from '@angular/core';
import { Router, ActivatedRoute, ParamMap } from '@angular/router';

import { APP_CONFIG, AppConfig } from '../app-config.module';

import { DataService } from '../data.service';

import { Observable } from 'rxjs/Observable';
import 'rxjs/add/operator/map';

@Component({
  selector: 'app-data-years',
  templateUrl: './data-years.component.html'
})
export class DataYearsComponent implements OnInit {

  tableData: any[];

  categories: any;

  years: number[];

  chartData: any[];

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private service: DataService,
    @Inject(APP_CONFIG) private config: AppConfig
  ) {}

  ngOnInit() {
    this.service.getYearsTableData().subscribe((data: any) => {
      this.categories = data && data.categories || {};
      this.tableData = data && data.sums.result || [];
      this.years = this.getYears();
      if (this.tableData.length) {
        this.calculateStats();
        this.chartData = this.getChartData();
      }
    });
  }

  getYears() {
    const years = [];
    for (let i = this.config.dataCurrentYear; i >= this.config.dataFirstYear; i--) {
      years.push(i);
    }
    return years;
  }

  getCellData(year, category) {
    const label = Object.keys(this.categories)
      .find((key) => this.categories[key] === category)
    || undefined;

    const result = this.tableData.find((val) => {
      return (val._id.year === year
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

  isNotFullYear(year) {
    return (year === this.config.dataCurrentYear)
      || (year === this.config.dataFirstYear);
  }

  private getChartData() {
    const earned = {};
    const spent = {};
    this.tableData.forEach((v, i) => {
      if (v._id.category === 'total-spent') {
        spent[v._id.year] = v.sum;
      }
      if (v.sum > 0 && (v._id.category !== 'total-spent'
        && v._id.category !== 'total-left')) {
        if (earned[v._id.year] === undefined) {
          earned[v._id.year] = 0;
        }
        earned[v._id.year] += v.sum;
      }
    });

    const getVals = data => {
      const output = [];
      (<any>Object).keys(data)
        .sort((a, b) => (b - a))
        .forEach((v, i) => output.push(data[v]));
      return output;
    };

    return [
      {
        data: getVals(earned),
        label: 'Earned'
      },
      {
        data: getVals(spent),
        label: 'Spent'
      }
    ];
  }

  private calculateStats() {
    const left = {};
    const spent = {};
    for (let i = 0; i < this.tableData.length; i++) {
      const v = this.tableData[i];
      if (left[v._id.year] === undefined) {
        left[v._id.year] = 0;
      }
      left[v._id.year] += v.sum;

      if (v.sum < 0) {
        if (spent[v._id.year] === undefined) {
          spent[v._id.year] = 0;
        }
        spent[v._id.year] += v.sum;
      }
    }

    Object.keys(spent).forEach((year) => {
      this.tableData.push({
        sum: spent[year],
        _id: {
          category: 'total-spent',
          year: +year
        }
      });
    });

    Object.keys(left).forEach((year) => {
      this.tableData.push({
        sum: left[year],
        _id: {
          category: 'total-left',
          year: +year
        }
      });
    });

    this.categories['total-spent'] = 'Amount Spent';
    this.categories['total-left'] = 'Amount Remain';
  }
}
