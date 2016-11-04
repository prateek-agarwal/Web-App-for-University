import { Component } from '@angular/core';
import { NavController } from 'ionic-angular';

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

  constructor(public navCtrl: NavController) {}

  ionViewDidLoad() {
    console.log('Hello OutstationGatepass Page');
  }

  apply() {
    // Move to final page with all the data.
  }

}
