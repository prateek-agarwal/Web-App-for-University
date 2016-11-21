import { Component } from '@angular/core';
import { AlertController } from 'ionic-angular';
import { NavController } from 'ionic-angular';


@Component({
  selector: 'page-page10',
  templateUrl: 'page10.html'
})
export class Page10{

   constructor(public navCtrl: NavController,public alerCtrl: AlertController) {
    
  }
  public event = {
    outtime: '05:00',
    intime: '07:00',
  }
  public warden = {
    // something to be added , currently no idea

  }
    doAlert() {
    let alert = this.alerCtrl.create({
      title: 'Request Send',
      message: 'Bon Voyage!',
      buttons: ['Ok']
    });
    alert.present()
}
}
