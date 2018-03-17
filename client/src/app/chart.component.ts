import { Component, Input, OnInit, ViewChild } from '@angular/core';
import { BaseChartDirective } from 'ng2-charts';

@Component({
  selector: 'app-bar-chart',
  template: `
  <div class="pt-3 pb-3">
    <h4>{{chartTitle}}</h4>
    <canvas baseChart *ngIf="chartData && chartLabels"
      [datasets]="chartData"
      [colors]="chartColors"
      [labels]="chartLabels"
      [options]="chartOptions"
      [legend]="chartLegend"
      [chartType]="chartType">
    </canvas>
  </div>
  `
})
export class ChartComponent {

  public chartOptions: any = {
    scaleShowVerticalLines: false,
    responsive: true
  };

  public chartLegend = true;

  public chartType = 'bar';

  public chartColors: any[] = [];

  private _chartData: any[];

  private _isLineChart = false;

  private _isPolarChart = false;

  @ViewChild(BaseChartDirective) private _chart: any;

  // tslint:disable-next-line:no-input-rename
  @Input('title') public chartTitle = '';

  /*
    eq ['2006', '2007', '2008', '2009', '2010', '2011', '2012']
  */
  // tslint:disable-next-line:no-input-rename
  @Input('labels') public chartLabels: string[];

  /*
    eq [
      {data: [65, 59, 80, 81, 56, 55, 40], label: 'Series A'},
      {data: [28, 48, 40, 19, 86, 27, 90], label: 'Series B'}
    ]
  */
  @Input('seriesData')
  set chartData(chartData) {
    this._chartData = chartData;
    if (chartData !== undefined) {
      chartData.forEach((val, i) => {
        chartData[i].data = val.data.map(v => {
          return Math.abs(Math.round(v * 100) / 100);
        });
      });
    }

    if (chartData && chartData.length === 2) {
      this.chartColors = [
        {
          backgroundColor: 'rgba(255, 99, 132, 0.7)',
          borderColor: 'rgba(255, 99, 132, 0.7)'
        },
        {
          backgroundColor: 'rgba(163, 215, 163, 0.75)',
          borderColor: 'rgba(75, 192, 192, 0.75)'
        }
      ];
    }

    if (this._chart !== undefined) {
      setTimeout(() => {
          this._chart.refresh();
      }, 10);
    }
  }

  get chartData() {
    return this._chartData;
  }

  @Input('isLine')
  set isLineChart(val: boolean) {
    this._isLineChart = val;
    this.chartType = 'line';
  }

  get isLineChart(): boolean {
    return this.isLineChart;
  }

  @Input('isPolar')
  set isPolarChart(val: boolean) {
    this._isPolarChart = val;
    this.chartType = 'polarArea';
  }

  get isPolarChart(): boolean {
    return this._isPolarChart;
  }

}
