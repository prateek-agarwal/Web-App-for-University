import { Component } from '@angular/core';
import { NavController, AlertController } from 'ionic-angular';

/*
  Generated class for the OutstationGatepass page.

  See http://ionicframework.com/docs/v2/components/#navigation for more info on
  Ionic pages and navigation.
*/
@Component({
  selector: 'page-outstation-gatepass',
  templateUrl: 'outstation-gatepass.html'
})
export class OutstationGatepass {

   public event = {
    depdate: '19-02-1990',
    outtime: '05:00',
    arrivedate: '19-02-1990',
    intime: '07:00',
  }
  public visitto = {
    // something to be added , currently no idea

  }
  constructor(public navCtrl: NavController, public alerCtrl: AlertController) {}

  ionViewDidLoad() {
    console.log('Hello OutstationGatepass Page');
  }

  apply() {
    // Move to final page with all the data.
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
