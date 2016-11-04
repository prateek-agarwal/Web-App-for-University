import { Component } from '@angular/core';
import { NavController } from 'ionic-angular';
import { Student } from '../../classes/student';
import { UserService } from '../../providers/user-service';

/*
  Generated class for the Timetable page.

  See http://ionicframework.com/docs/v2/components/#navigation for more info on
  Ionic pages and navigation.
*/
@Component({
  selector: 'page-timetable',
  templateUrl: 'timetable.html'
})
export class Timetable {

  student: Student;

  constructor(public navCtrl: NavController, private userService: UserService) {


  }

  ionViewDidLoad() {
    console.log('Hello Timetable Page');

    this.userService.getUser().then(
      s => {console.log("At timetable page: ", JSON.stringify(s)); }
    );

  }

}
