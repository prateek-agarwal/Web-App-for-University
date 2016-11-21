import { Component } from '@angular/core';

import { NavController } from 'ionic-angular';
import { Page8 } from '../page8/page8';
import {Page7} from '../page7/page7';



@Component({
  selector: 'page-page6',
  templateUrl: 'page6.html'
})
export class Page6 {

  constructor(public navCtrl: NavController) {
    
  }
  goToOtherlocalPage(){
    this.navCtrl.push(Page7);
    // for local gatepass
 }
   goToOtherOutstationPage(){
    this.navCtrl.push(Page8);
    // for outstation gatepass
  }
}
