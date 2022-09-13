import { Component, OnInit, ViewChild, ElementRef } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-list-page',
  templateUrl: './list-page.component.html',
  styleUrls: ['./list-page.component.scss']
})
export class ListPageComponent implements OnInit {
  @ViewChild('uploader', { static: false, read: ElementRef }) uploader: any;
  @ViewChild('avatar', { static: false, read: ElementRef }) avatar: any;

  data: any = null
  userid: string = ""
  baseUrl = 'http://localhost:8001/api/'
  public uploadAvatar() {
    let formData: FormData = new FormData();
    formData.append('image', this.avatar.nativeElement.files[0])
    this.http.post<any[]>(`${this.baseUrl}users/${this.userid}`,
      formData)
      .subscribe({
        next: (resp) => {
         this.loadData()
        },
        error: (error) => {
          console.error(error);

        }
      });
  }
  public update(elem: any) {
    elem.setAttribute('contenteditable', false)
    let field: string = elem.getAttribute('data-field')
    let value = elem.innerHTML
    let id = elem.getAttribute('data-row')
    let params: any = []
    params[field] = value
    this.http.put<any[]>(`${this.baseUrl}users/${id}`,
      Object.assign({}, params)
    )
      .subscribe({
        next: (resp) => {
          this.data = resp
        },
        error: (error) => {
          console.error(error);

        }
      });
  }
  public setUserId(userid: string) {
    this.userid = userid
    this.avatar.nativeElement.click()
  }
  uploadExcel() {

    let formData: FormData = new FormData();

    formData.append('uploaded_file', this.uploader.nativeElement.files[0])

    this.http.post(`${this.baseUrl}users`, formData).subscribe(
      (response) => {
        this.loadData()
      }
    )
  }
  downloadExcel() {
    this.http.get(`${this.baseUrl}users/1`, {
      responseType: 'blob'
    }
    ).subscribe((response) => {
      let blob = new Blob([response], { type: 'application/ms-excel' });
      let url = window.URL.createObjectURL(blob);
      var link = document.createElement('a');
      link.href = url;
      link.download = "users.xls";
      link.click();
    });
  }
  onDbClick(elem: any) {
    elem.setAttribute('contenteditable', true)
    elem.focus()
  }
  public loadData() {
    this.http.get<any[]>(this.baseUrl + 'users')
      .subscribe({
        next: (resp) => {
          this.data = resp
        },
        error: (error) => {
          console.error(error);

        }
      });
  }
  constructor(public http: HttpClient) {
  }

  ngOnInit(): void {
    this.loadData()
  }

}
