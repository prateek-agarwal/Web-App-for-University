import { Component } from '@angular/core';

import { NavController } from 'ionic-angular';
import { StudentService } from '../../providers/student-service';

@Component({
  selector: 'page-home',
  templateUrl: 'home.html',
  providers: [StudentService]
})
export class HomePage {

  public people: any;
  constructor(public navCtrl: NavController, public peopleService: StudentService) {

  }

  ionViewDidLoad() {
    
  }
  loadPeople(userid) {
    this.peopleService.load(userid)
        .then(data => {
          this.people = data;
        })
  }

}
