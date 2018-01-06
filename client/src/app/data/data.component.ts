import { Component } from '@angular/core';

@Component({
  templateUrl: './data.component.html'
})
export class DataComponent {

  isToggleClicked = false;

  onToggleClick() {
    this.isToggleClicked = !this.isToggleClicked;
  }
}
