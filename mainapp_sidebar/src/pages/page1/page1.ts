import { Component } from '@angular/core';

import {Page5} from '../page5/page5';

import { NavController } from 'ionic-angular';

@Component({
  selector: 'page-page1',
  templateUrl: 'page1.html'
})

export class Page1 {

  lists = [
    {id : "Monday",
      bgcolor: '#fb9667',
      nclass:'4 lectures'},
    
    {id : "Tuesday",
      bgcolor: 'mediumpurple',
      nclass:'7 lectures'},
    
    {id : "Wednesday",
      bgcolor: 'orange',
      nclass:'6 lectures'},
    
    {id : "Thrusday",
      bgcolor: 'mediumturquoise',
      nclass:'8 lectures'},
    
    {id : "Friday",
      bgcolor: ' lightcoral',
      nclass:'8 lectures'},
 
    {id : "Saturday",
      bgcolor: 'yellowgreen',
      nclass:'8 lectures'}
 
    
  ];
  constructor(public navCtrl: NavController) {
    
  }
  goToOtherPage(){
    this.navCtrl.push(Page5);
  }
}
