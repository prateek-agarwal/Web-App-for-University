import { Component } from '@angular/core';
import { AlertController } from 'ionic-angular';
import { NavController } from 'ionic-angular';


@Component({
  selector: 'page-page8',
  templateUrl: 'page8.html'
})
export class Page8{

  
  constructor(public navCtrl: NavController,public alerCtrl: AlertController) {
    
  }
  public event = {
    depdate: '19-02-1990',
    outtime: '05:00',
    arrivedate: '19-02-1990',
    intime: '07:00',
  }
  public visitto = {
    // something to be added , currently no idea

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
