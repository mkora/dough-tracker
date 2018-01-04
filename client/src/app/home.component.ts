import { Component, OnInit, Inject } from '@angular/core';
import { APP_CONFIG, AppConfig } from './app-config.module';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
})
export class HomeComponent implements OnInit {

  currentYear: number;

  constructor(
    @Inject(APP_CONFIG) private config: AppConfig
  ) { }

  ngOnInit() {
    this.currentYear = this.config.dataCurrentYear;
  }

}
