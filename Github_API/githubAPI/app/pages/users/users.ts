import { Component } from '@angular/core';
import { NavController } from 'ionic-angular';
import { User} from '../../models/user';
import {UserDetailsPage} from '../user-details/user-details';


// Import GithubUsers provider
import {GithubUsers} from '../../providers/github-users/github-users';
/*
  Generated class for the UsersPage page.

  See http://ionicframework.com/docs/v2/components/#navigation for more info on
  Ionic pages and navigation.
*/
@Component({
  templateUrl: 'build/pages/users/users.html',

  // Add the GithubUsers provider as part of our page component
  providers: [GithubUsers]
})
export class UsersPage {
    users: User[];

  // Inject the GithubUsers in the constructor of our page component
  constructor(public nav: NavController, githubUsers: GithubUsers) {
    // Test whether the github provider returns data
    githubUsers
      .load()
      .then(users => this.users = users);
  }
  goToDetails(event, login) {
    this.nav.push(UserDetailsPage, {
      login: login
    });

}
}
