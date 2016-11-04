import { Component, ViewChild } from '@angular/core';
import { NavController } from 'ionic-angular';
import { GatepassStep1 } from '../gatepass-step1/gatepass-step1';
import { Gatepass } from '../../classes/gatepass';
import { Login } from '../login/login';
import { UserService } from '../../providers/user-service';
import { GatepassService } from '../../providers/gatepass-service';

/*
  Generated class for the GatepassHome page.

  See http://ionicframework.com/docs/v2/components/#navigation for more info on
  Ionic pages and navigation.
*/
@Component({
  selector: 'page-gatepass-home',
  templateUrl: 'gatepass-home.html'
})
export class GatepassHome {


  public student: any;
  public gatepass: Gatepass;

  constructor(public navCtrl: NavController,
    private userService: UserService,
    public gatepassService: GatepassService) {
      this.userService.getUser().then(s => {
        if (s != null) {
          this.student = s;
        }
        else {
          // Navigate back to login page.
          // TODO
          navCtrl.setRoot(Login);
        }
      });
  }

  ionViewDidLoad() {
    console.log('Hello GatepassHome Page');

    // TODO
    // Check for internet connection, without that this module won't work.

    this.student.checkGatepassStatus(this.gatepassService);

  }

  applyGatepass() {
    // Navigate to fill the form.

    this.navCtrl.push(GatepassStep1, {
      student: this.student,
    });
  }

}
