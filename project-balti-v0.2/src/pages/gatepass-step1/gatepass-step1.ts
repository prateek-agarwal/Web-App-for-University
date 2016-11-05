import { Component } from '@angular/core';
import { NavController, NavParams } from 'ionic-angular';
import { Gatepass } from '../../classes/gatepass';
import { Student } from '../../classes/student';
import { GatepassService } from '../../providers/gatepass-service';
import { LocalGatepass } from '../local-gatepass/local-gatepass';
import { OutstationGatepass } from '../outstation-gatepass/outstation-gatepass';


/*
  Generated class for the GatepassStep1 page.

  See http://ionicframework.com/docs/v2/components/#navigation for more info on
  Ionic pages and navigation.
*/
@Component({
  selector: 'page-gatepass-step1',
  templateUrl: 'gatepass-step1.html'
})
export class GatepassStep1 {
  student: Student;
  gatepass: Gatepass;

  constructor(public navCtrl: NavController, public navParams: NavParams, public gatepassService: GatepassService) {
    this.student = navParams.get('student');
  }

  ionViewDidLoad() {
    console.log('Hello GatepassStep1 Page');

    // Call a getPreAplly, to get all details weather gatepass can be applied or not.
    this.gatepassService.getPreApply();
  }

  localGatepass() {
    // Auto fill neccessary details.
    // Fill the class.

    this.navCtrl.push(LocalGatepass, {
      student: this.student,
      gatepass: this.gatepass
    });
  }

  outstationGatepass() {
    this.navCtrl.push(OutstationGatepass, {
      student: this.student,
      gatepass: this.gatepass
    });
  }

}
