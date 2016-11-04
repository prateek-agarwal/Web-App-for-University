import { Component } from '@angular/core';
import { NavController } from 'ionic-angular';
import { GatepassService } from '../../providers/gatepass-service';
import { Login2 } from '../login2/login2';
/*
  Generated class for the Login page.

  See http://ionicframework.com/docs/v2/components/#navigation for more info on
  Ionic pages and navigation.
*/
@Component({
  selector: 'page-login',
  templateUrl: 'login.html'
})
export class Login {

  public email_id: string;
  public msg: string;

  constructor(public navCtrl: NavController, private gatepassService: GatepassService ) {
    this.email_id = '';
  }

  ionViewDidLoad() {
    console.log('Hello Login Page');

  }

  public validateUser() {
    if (this.email_id == '')
      return;
    this.gatepassService.checkUser(this.email_id)
      .subscribe(
        data => this.nextStep(data),
        error => this.msg = <any>error
      );
      /*.then(data => {
        this.nextStep(data)
      }, error => this.msg = "Sorry, Invalid User");
      */
  }

  private nextStep(data: any) {
    console.log('Got it now!!!');

    // TODO error checking weather the email_id is correct or not
    this.msg = data['message'];

    this.navCtrl.push(Login2, {
        email_id: this.email_id
    });
  }

}
