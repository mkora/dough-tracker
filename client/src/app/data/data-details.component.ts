import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, ParamMap } from '@angular/router';

import { DataService } from '../data.service';

@Component({
  selector: 'app-data-details',
  templateUrl: './data-details.component.html'
})
export class DataDetailsComponent implements OnInit {

  year: number;

  month: number;

  tableData: any[];

  categories: any;

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private service: DataService
  ) {}

  ngOnInit() {
    this.route.paramMap.switchMap((params: ParamMap) => {
      this.year = +params.get('year');
      this.month = +params.get('month');
      return this.service
        .getDetailsTableData(this.year, this.month);
    }).subscribe((data: any) => {
      this.categories = data && data.categories || {};
      this.tableData = data && data.sums.result || [];
      if (this.tableData.length) {
        this.calculateStats();
      }
    });
  }

  getCategoryTitle(category) {
    return this.categories[category] || 'n/a';
  }

  isTotalRow(category) {
    return category.search('total') !== -1;
  }

  private calculateStats() {
    let left = 0;
    let spent = 0;
    let earned = 0;
    for (let i = 0; i < this.tableData.length; i++) {
      const v = this.tableData[i];
      if (v.type < 1) {
        spent += v.sum * v.type;
      } else {
        earned += v.sum * v.type;
      }
      left += v.sum * v.type;
    }

    // add average column
    this.tableData.push({
      category: 'total-earned',
      month: this.month,
      year: this.year,
      sum: Math.abs(earned),
      title: 'Amount Earned',
      type: (earned < 0) ? -1 : 1
    });

    this.tableData.push({
      category: 'total-spent',
      month: this.month,
      year: this.year,
      sum: Math.abs(spent),
      title: 'Amount Spent',
      type: (spent < 0) ? -1 : 1
    });

    this.tableData.push({
      category: 'total-left',
      month: this.month,
      year: this.year,
      sum: Math.abs(left),
      title: 'Amount Left',
      type: (left < 0) ? -1 : 1
    });

    this.categories['total-earned'] = 'Amount Earned';
    this.categories['total-spent'] = 'Amount Spent';
    this.categories['total-left'] = 'Amount Left';
  }

}
