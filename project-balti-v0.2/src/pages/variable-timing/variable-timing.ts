import { Component } from '@angular/core';
import { NavController, NavParams, AlertController } from 'ionic-angular';
import { Gatepass } from '../../classes/gatepass';
import { Student } from '../../classes/student';

/*
  Generated class for the VariableTiming page.

  See http://ionicframework.com/docs/v2/components/#navigation for more info on
  Ionic pages and navigation.
*/
@Component({
  selector: 'page-variable-timing',
  templateUrl: 'variable-timing.html'
})
export class VariableTiming {
  student: Student;
  gatepass: Gatepass;
public warden = {
    // something to be added , currently no idea

  }
  public event = {
    outtime: '05:00',
    intime: '07:00',
  }
  constructor(public navCtrl: NavController, public navParams: NavParams, public alerCtrl: AlertController) {
    this.student = navParams.get("student");
    this.gatepass = navParams.get("gatepass");
  }

  ionViewDidLoad() {
    console.log('Hello VariableTiming Page');
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
