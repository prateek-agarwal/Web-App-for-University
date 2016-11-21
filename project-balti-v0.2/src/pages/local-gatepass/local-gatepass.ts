import { Component } from '@angular/core';
import { NavController, NavParams } from 'ionic-angular';
import { Gatepass } from '../../classes/gatepass';
import { Student } from '../../classes/student';
import { GatepassFinal } from '../gatepass-final/gatepass-final';
import { VariableTiming } from '../variable-timing/variable-timing';

/*
  Generated class for the LocalGatepass page.

  See http://ionicframework.com/docs/v2/components/#navigation for more info on
  Ionic pages and navigation.
*/
@Component({
  selector: 'page-local-gatepass',
  templateUrl: 'local-gatepass.html'
})
export class LocalGatepass {
  student: Student;
  gatepass: Gatepass;

  constructor(public navCtrl: NavController, public navParams: NavParams) {
    this.student = navParams.get("student");
    this.gatepass = navParams.get("gatepass");
  }

  ionViewDidLoad() {
    console.log('Hello LocalGatepass Page');
  }

  fixedTiming() {
    // Add fixed timing details.
    // Directly go to the final page.
    // TODO

    this.navCtrl.push(GatepassFinal, {
      student: this.student,
      gatepass: this.gatepass
    });

  }

  variableTiming() {
    // Add variable timing data and then navigate to gatepassvartime2

    this.navCtrl.push(VariableTiming, {
      student: this.student,
      gatepass: this.gatepass
    });
  }

}
