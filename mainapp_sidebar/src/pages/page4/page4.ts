import { Component } from '@angular/core';

import { NavController } from 'ionic-angular';

@Component({
  selector: 'page-page4',
  templateUrl: 'page4.html'
})
export class Page4 {

  constructor(public navCtrl: NavController) {
    
  }
  public book = {
    bookname1:'Operating System Concepts',
    issueddate1:'8/11/16',
    duedate1:'15/11/16',
    bookname2:'Digital Logic Circuits',
    issueddate2:'8/11/16',
    duedate2:'15/11/16',
    fine: '300Rs'
  }
  
}
