import { Component, OnInit, Inject } from '@angular/core';
import { Router, ActivatedRoute, ParamMap } from '@angular/router';

import { APP_CONFIG, AppConfig } from '../app-config.module';

import { DataService } from '../data.service';

import { Observable } from 'rxjs/Observable';
import 'rxjs/add/operator/map';


@Component({
  selector: 'data-years',
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
      if (!this.tableData.length) return;
      this.calculateStats();
      this.chartData = this.getChartData();
    });

  }

  getYears() {
    let years = [];
    for (let i = this.config.dataCurrentYear; i >= this.config.dataFirstYear; i--) {
      years.push(i);
    }
    return years;
  }

  getCellData(year, category){
    let label;
    for (label in this.categories)
      if (this.categories[label] === category) break;

    for (let i in this.tableData) {
      let v = this.tableData[i];
      if (v._id.year === year && v._id.category === label) {
        return v.sum;
      }
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

  isNotFullYear(year) {
    return year === this.config.dataCurrentYear ||
      year === this.config.dataFirstYear;

  }

  private getChartData() {
    const earned = {}, spent = {};
    this.tableData.forEach((v, i) => {
      if (v._id.category === 'total-spent')
        spent[v._id.year] = v.sum;
      if (v.sum > 0 && (v._id.category !== 'total-spent' && v._id.category !== 'total-left')) {
        if (earned[v._id.year] === undefined) earned[v._id.year] = 0;
        earned[v._id.year] += v.sum;
      }
    });

    let getVals = data => {
      let output = [];
      (<any>Object).keys(data)
      .sort((a, b) => b - a)
      .forEach((v, i) => {
        output.push(data[v]);
      });
      return output;
    }
    return [
      {data: getVals(earned), label: 'Earned'},
      {data: getVals(spent), label: 'Spent'}
    ];
  }

  private calculateStats() {

    const left = {}, spent = {};
    this.tableData.forEach((v, i) => {
      if (left[v._id.year] === undefined) left[v._id.year] = 0;
      left[v._id.year] += v.sum;

      if (v.sum < 0) {
        if (spent[v._id.year] === undefined) spent[v._id.year] = 0;
        spent[v._id.year] += v.sum;
      }
    });

    for (let year in spent) {
      this.tableData.push({
        sum: spent[year],
        _id: {
          category: 'total-spent',
          year: +year
        }
      })
    }

    for (let year in left) {
      this.tableData.push({
        sum: left[year],
        _id: {
          category: 'total-left',
          year: +year
        }
      });
    }

    this.categories['total-spent'] = 'Amount Spent';
    this.categories['total-left'] = 'Amount Remain';

  }
}
