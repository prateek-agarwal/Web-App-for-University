import { Component } from '@angular/core';
import { NavController } from 'ionic-angular';

/*
  Generated class for the GatepassPage page.

  See http://ionicframework.com/docs/v2/components/#navigation for more info on
  Ionic pages and navigation.
*/
@Component({
  selector: 'page-gatepass-page',
  templateUrl: 'gatepass-page.html'
})
export class GatepassPage {

  constructor(public navCtrl: NavController) {}

  ionViewDidLoad() {
    console.log('Hello GatepassPage Page');
  }

}
