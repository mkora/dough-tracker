import { Component, Input } from '@angular/core';

@Component({
  selector: 'app-data-item',
  template: `
  <span
    [innerText]="data | currency: 'USD':true | replace: '-': ' '"
    [ngClass]="data < 0 ? 'text-danger' : 'text-success'">
  </span>
  `
})
export class DataItemComponent {
  @Input() data: number;
}
