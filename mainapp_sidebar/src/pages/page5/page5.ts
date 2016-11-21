import { Component } from '@angular/core';

import { NavController } from 'ionic-angular';

@Component({
  selector: 'page-page5',
  templateUrl: 'page5.html'
})
export class Page5 {

  lists = [
    {
    id: "8:30-9:30",
    id2 :"CS231",
    id3 :"LT211"
  }];
  days =[
    "Monday"
    
  ];
  constructor(public navCtrl: NavController) {
    
  }
}
