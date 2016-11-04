import { Gatepass } from './gatepass';
import { GatepassService } from '../providers/gatepass-service';

export class Student {
  // TODO make everything except api key private.
  public name: string;
  public enrollment_no: string;
  public email_id: string;
  public api_key: string;

  constructor(name, enrollment_no, email_id, api_key) {
    this.name = name;
    this.enrollment_no = enrollment_no;
    this.email_id = email_id;
    this.api_key = api_key;
  }


  getTimetable() {

  }

  getAttendance() {

  }

  getIssuedBookDetails() {

  }

  getFine() {

  }

  searchBook() {

  }

  applyGatepass(gatepas: Gatepass) {

  }

  checkGatepassStatus(gatepassService: GatepassService) {

    gatepassService.checkStatus(this.email_id, this.api_key);
  }
}
