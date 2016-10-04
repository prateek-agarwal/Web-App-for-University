import { NavController } from 'ionic-angular';
import { StudentService } from '../../providers/student-service';
export declare class HomePage {
    navCtrl: NavController;
    peopleService: StudentService;
    people: any;
    constructor(navCtrl: NavController, peopleService: StudentService);
    ionViewDidLoad(): void;
    loadPeople(userid: any): void;
}
