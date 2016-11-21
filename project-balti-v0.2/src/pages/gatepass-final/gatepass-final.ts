import { Component } from '@angular/core';
import { NavController, NavParams } from 'ionic-angular';
import { Gatepass } from '../../classes/gatepass';
import { Student } from '../../classes/student';


/*
  Generated class for the GatepassFinal page.

  See http://ionicframework.com/docs/v2/components/#navigation for more info on
  Ionic pages and navigation.
*/
@Component({
  selector: 'page-gatepass-final',
  templateUrl: 'gatepass-final.html'
})
export class GatepassFinal {
  student: Student;
  gatepass: Gatepass;

  constructor(public navCtrl: NavController, public navParams: NavParams) {
    this.student = navParams.get("student");
    this.gatepass = navParams.get("gatepass");
  }

  ionViewDidLoad() {
    console.log('Hello GatepassFinal Page');
  }

  sendGatepassRequest() {
    this.student.applyGatepass(this.gatepass);
  }

}
