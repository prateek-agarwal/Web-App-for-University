import { Component } from '@angular/core';
import { AlertController } from 'ionic-angular';
import { NavController } from 'ionic-angular';


@Component({
  selector: 'page-page9',
  templateUrl: 'page9.html'
})
export class Page9{

  
  constructor(public navCtrl: NavController,public alerCtrl: AlertController) {
    
  } 
  public fixed = {
    depdate:'8/11/16',
    arrivaldate:'8/11/16',
    outtime: '05:00 PM',
    intime: '09:00 PM',
  }
  
  doAlert() {
    let alert = this.alerCtrl.create({
      title: 'Request Send',
      message: 'Bon Voyage!',
      buttons: ['Ok']
    });
    alert.present()
}

 
  //  goToOtherPage(){
  //   this.navCtrl.push(Page7);
  //   // for local gatepass
  // }
  //  goToOtherPage1(){
  //   this.navCtrl.push(Page8);
  //   // for outstation gatepass
  // }
}
