import { Component } from '@angular/core';

import { NavController} from 'ionic-angular';
import {Page6} from '../page6/page6';

@Component({
  selector: 'page-page2',
  templateUrl: 'page2.html'
})
export class Page2 {
 
  constructor(public navCtrl: NavController) {
    
  }
  goToOtherPage(){
    this.navCtrl.push(Page6);
  }
}
