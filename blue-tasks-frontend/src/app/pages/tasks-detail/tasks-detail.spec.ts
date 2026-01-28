import { ComponentFixture, TestBed } from '@angular/core/testing';

import { TasksDetail } from './tasks-detail';

describe('TasksDetail', () => {
  let component: TasksDetail;
  let fixture: ComponentFixture<TasksDetail>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [TasksDetail]
    })
    .compileComponents();

    fixture = TestBed.createComponent(TasksDetail);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
