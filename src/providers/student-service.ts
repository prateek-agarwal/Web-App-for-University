import { Injectable } from '@angular/core';
import { Http, Headers } from '@angular/http';
import 'rxjs/add/operator/map';

/*
  Generated class for the StudentService provider.

  See https://angular.io/docs/ts/latest/guide/dependency-injection.html
  for more info on providers and Angular 2 DI.
*/
@Injectable()
export class StudentService {

  data: any;

  constructor(public http: Http) {
    console.log('Hello StudentService Provider');
  }

  load(userid: String) {
  /*if (this.data) {
    // already loaded data
    return Promise.resolve(this.data);
  }
  */
  // var headers = new Headers();
  //    headers.append('Content-Type', 'application/x-www-form-urlencoded');
  // var body = 'user_id=U101114FCS223';
  // don't have the data yet
  const url = `http://localhost:8080/viewStudent?user_id=` + userid.toString();

  return new Promise(resolve => {
    // We're using Angular HTTP provider to request the data,
    // then on the response, it'll map the JSON data to a parsed JS object.
    // Next, we process the data and resolve the promise with the new data.
    this.http.get(url)
      .map(res => res.json())
      .subscribe(data => {
        // we've got back the raw data, now generate the core schedule data
        // and save the data for later reference
        this.data = data;
        resolve(this.data);
      });
  });
}

}
