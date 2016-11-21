import { Component } from '@angular/core';

import { NavController } from 'ionic-angular';
import { Page9 } from '../page9/page9';
import {Page10} from '../page10/page10';


@Component({
  selector: 'page-page7',
  templateUrl: 'page7.html'
})
export class Page7 {

  
  constructor(public navCtrl: NavController) {
    
  }
   goToFixedPage(){
    this.navCtrl.push(Page9);
    // for fixed time gatepass
  }
   goToVariablePage(){
    this.navCtrl.push(Page10);
    // for variable time gatepass
  }
}
