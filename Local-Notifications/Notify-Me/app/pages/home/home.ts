import {Component} from "@angular/core";
import {NavController, AlertController} from 'ionic-angular';
import {LocalNotifications} from 'ionic-native';
//import {Alert} from 'ionic-angular';
//import {Page, Alert, NavController} from 'ionic-angular';


@Component({
    templateUrl: 'build/pages/home/home.html'
})

// Schedule a single notification

export class HomePage {
 
    constructor(public navController: NavController, public alertCtrl: AlertController) {
        LocalNotifications.on("click", (notification, state) => {
            let alert = this.alertCtrl.create({
                title: "Notification Clicked",
                subTitle: "You just clicked the scheduled notification",
                buttons: ["OK"]
            });
            alert.present();
        });
    }
 
    public schedule() {
        LocalNotifications.schedule({
            id: 1,
            title: "Test Title",
            text: "Delayed Notification",
            at: new Date(new Date().getTime() + 3600),
            sound: null
        });
    }
 
}