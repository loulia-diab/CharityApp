import 'dart:math';

import 'package:charity_project/app_colors.dart';
import 'package:charity_project/view/app_text_style.dart';
import 'package:charity_project/view/background.dart';
import 'package:charity_project/view/charity_fund_page.dart';
import 'package:charity_project/view/input_decoraition.dart';
import 'package:charity_project/view/sadakah_page.dart';
import 'package:flutter/material.dart';

class VolunteerRequestPage extends StatefulWidget {
   VolunteerRequestPage({super.key});
final formkey = GlobalKey<FormState>();

  @override
  State<VolunteerRequestPage> createState() => _VolunteerRequestPageState();
}

class _VolunteerRequestPageState extends State<VolunteerRequestPage> {
  TextEditingController firstname = TextEditingController();
TextEditingController lastname = TextEditingController();
TextEditingController birthDate = TextEditingController();
TextEditingController address = TextEditingController();
TextEditingController job = TextEditingController();
TextEditingController phoneNumber = TextEditingController();
TextEditingController details = TextEditingController();
TextEditingController experiencedetails = TextEditingController();
String? selectedEducation;
String? experience;
String? Time;
DateTime? selectedDate;
String? gender ;
bool FieldWork = false;
bool Administrative = false;
bool Awareness = false;
bool Media = false;
bool Design = false;
bool Technical = false;
bool Sunday = false;
bool Monday = false;
bool Tuesday = false;
bool Wednisdey = false;
bool Thursday = false;
bool Friday = false;
bool Saturday = false;
bool Agree = false;
List<String> study = [
  "School Student","University Student","Diploma","Bachelor's Degree","Master's Degree","None"
];


void pickedBirthDate ()async {
  DateTime? pickedDate = await showDatePicker(context: context,
  initialDate: DateTime.now(),
   firstDate: DateTime(1900), lastDate: DateTime.now(),
   
   
    builder: (BuildContext context, Widget? child) {
      return Theme(
        data: Theme.of(context).copyWith(
          colorScheme: ColorScheme.light(
            primary:AppColors.primary, // لون الترويسة والأزرار
            onPrimary: Colors.white,    // لون النص فوق اللون الأساسي
            onSurface: Colors.black,    // لون النص الأساسي
          ),
          textButtonTheme: TextButtonThemeData(
            style: TextButton.styleFrom(
              foregroundColor: AppColors.primary, // لون زر "CANCEL" و "OK"
            ),
          ),
        ),
        child: child!,
      );
    },

   );
if (pickedDate != null) {
  setState(() {
    selectedDate = pickedDate;
    birthDate.text = "${pickedDate.day}/${pickedDate.month}/${pickedDate.year}";
  });
}

}
void submitForm(){
  if (formkey.currentState!.validate()) {
  Navigator.push((context), MaterialPageRoute(builder: (context)=> CharityFundPage()));
  }
}

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
                backgroundColor: AppColors.white,
                title: Text('Volunteer Request',style: AppTextStyle.a,),
              ),
      body: BackgroundWrapper(
        child: Form(
          key: formkey,
          child: Column(
            children: [
              
              Expanded(
                child: SingleChildScrollView(
                  child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                         Padding(
                        padding: const EdgeInsets.only(top:5,left: 20,right: 20),
                        child: Text('Full Name',style: AppTextStyle.helpReq,),
                      ),
                      Padding(
                        padding: const EdgeInsets.all(8.0),
                        child: Row(
                          children: [
                Expanded(
                  child: TextFormField(
                    
                    cursorColor: AppColors.primary,
                    
                    controller: firstname,
                    decoration: AppInputDecoration.defaultDecoration.copyWith(
                  label: Text("First Name")
                    ),
                    validator: (value) {
                      if (value == null || value.isEmpty) {
                        return 'please enter your first name';
                      }
                      return null;
                    },
                  
                  ),
                ),
                
                
                SizedBox(width: 20,)
                , Expanded(
                   child: TextFormField(
                    
                    cursorColor: AppColors.primary,
                    
                    controller: lastname,
                    decoration: AppInputDecoration.defaultDecoration.copyWith(
                               label: Text("Last Name")
                    ),
                    validator: (value) {
                      if (value == null || value.isEmpty) {
                        return 'please enter your last name';
                      }
                      return null;
                    },
                               
                               ),
                 ),
                          ],
                        ),
                      ),
                      SizedBox(height: 6),
                
                
                      
                Row(
                children: [
                    Padding(
                padding: const EdgeInsets.only(top:5,left: 20,right: 20),
                child: Text('Gender : ',style: AppTextStyle.helpReq,),
                          ),
                          Padding(
                padding: const EdgeInsets.only(top:7),
                child: Row(
                  children: [
                    Radio(activeColor: AppColors.primary,
                      value: "male", groupValue: gender, onChanged: (val)=>setState(() {
                      gender =val as String;
                    })),
                    Text("male")
                  ],
                ),
                          ),
                
                Padding(
                  padding:const EdgeInsets.only(top:7),
                  child: Row(
                    children: [
                      Radio(activeColor: AppColors.primary,
                        value: "female", groupValue: gender, onChanged: (val)=>setState(() {
                        gender =val as String;
                      })
                      ),
                      Text("female")
                    ],
                  ),
                ),]
                ),
                
                
                Padding(
                  padding:  const EdgeInsets.only(left: 20,right: 20,top: 10),
                  child: TextFormField(
                    decoration: AppInputDecoration.defaultDecoration.copyWith(
                      prefixIcon: Icon(Icons.date_range),
                      
                    ),
                    controller: birthDate,
                    onTap: pickedBirthDate,
                    readOnly: true,
                    validator: (value) {
                      if (selectedDate == null) {
                        return 'please enter your date';
                      }
                      return null;
                    },
                    
                  ),
                ),
                
                Column(
                   crossAxisAlignment: CrossAxisAlignment.start,
                   children: [
                      Padding(
                 padding: const EdgeInsets.only(left: 20,right: 20,bottom: 20,top: 20),
                 child: Text('Your address',style: AppTextStyle.helpReq,),
                           ),
                     Padding(
                       padding:  const EdgeInsets.only(left: 20,right: 20),
                       child: TextFormField(
                        controller: address,
                        maxLines: 2,
                       keyboardType: TextInputType.text,
                         decoration: AppInputDecoration.defaultDecoration.copyWith(
                         label: Text("Your address")
                           
                         ),
                         validator: (value) {
                           if (value == null || value.isEmpty) {
                             return 'please enter your address';
                           }
                         },
                       ),
                     ),
                   ],
                 ),
                        
                  Column(
                   crossAxisAlignment: CrossAxisAlignment.start,
                   children: [
                      Padding(
                 padding: const EdgeInsets.only(left: 20,right: 20,bottom: 20,),
                 child: Text('Volunteer Qualifications:',style: AppTextStyle.helpReq,),
                           ),
                           SizedBox(height: 2,),
                            Padding(
                 padding: const EdgeInsets.only(left: 20,right: 20,bottom: 10),
                 child: Text('Study Qualification:',style: AppTextStyle.helpReq,),
                           ),
                           Padding(
                             padding:  const EdgeInsets.only(left: 20,right: 20),
                             child: DropdownButtonFormField(decoration: AppInputDecoration.defaultDecoration.copyWith(
                               label: Text("Study")
                             ),
                              value: selectedEducation,items: study.map((studyType){
                             return DropdownMenuItem(child: Text(studyType),value: studyType,);
                             }).toList()
                             , onChanged: (value)=>setState(() {
                               selectedEducation = value;
                             })),
                           )
                     ,Padding(
                       padding:  const EdgeInsets.only(left: 20,right: 20,top: 20),
                       child: TextFormField(
                        controller: job,
                        
                       keyboardType: TextInputType.text,
                         decoration: AppInputDecoration.defaultDecoration.copyWith(
                         label: Text("Job (if you have):")
                           
                         ),
                         
                       ),
                     ),
                   ],
                 ),
                
                
                
                
                Padding(
                  padding: const EdgeInsets.only(left: 20,right: 20,bottom: 20,top: 20),
                  child: Text('Preferred Volunteering Type:',style: AppTextStyle.helpReq,),
                ),
                Row(
                  children: [
                    Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Padding(
                          padding: const EdgeInsets.only(left: 20,right: 20),
                          child: Row(
                            children: [
                              Checkbox(activeColor: AppColors.primary,
                                value: FieldWork, onChanged: (val){
                                setState(() {
                                  FieldWork = val!;
                                });
                              }),
                              SizedBox(width: 10,),
                              Text("Field Work",style: AppTextStyle.helpReq,)
                            ],
                          ),
                        ),
                        SizedBox(height: 5,),
                                
                                
                        Padding(
                          padding: const EdgeInsets.only(left: 20,right: 20),
                          child: Row(
                            children: [
                              Checkbox(activeColor: AppColors.primary,
                                value: Administrative, onChanged: (val){
                                setState(() {
                                  Administrative = val!;
                                });
                              }),
                              SizedBox(width: 10,),
                              Text("Administrative",style: AppTextStyle.helpReq,)
                            ],
                          ),
                        ),
                                
                                
                                
                        SizedBox(height: 5,),
                                
                                
                        Padding(
                          padding: const EdgeInsets.only(left: 20,right: 20),
                          child: Row(
                            children: [
                              Checkbox(activeColor: AppColors.primary,
                                value: Awareness, onChanged: (val){
                                setState(() {
                                  Awareness = val!;
                                });
                              }),
                              SizedBox(width: 10,),
                              Text("Awareness",style: AppTextStyle.helpReq,)
                            ],
                          ),
                        ),
                      ],
                    ),
                            
                            
                            
                            
                            
                    SizedBox(height: 5,),
                            
                            
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Padding(
                          padding: const EdgeInsets.only(left: 20,right: 20),
                          child: Row(
                            children: [
                              Checkbox(activeColor: AppColors.primary,
                                value: Media, onChanged: (val){
                                setState(() {
                                  Media = val!;
                                });
                              }),
                              SizedBox(width: 10,),
                              Text("Media",style: AppTextStyle.helpReq,)
                            ],
                          ),
                        ),
                                
                                
                                
                        SizedBox(height: 5,),
                                
                                
                        Padding(
                          padding: const EdgeInsets.only(left: 20,right: 20),
                          child: Row(
                            children: [
                              Checkbox(activeColor: AppColors.primary,
                                value: Design, onChanged: (val){
                                setState(() {
                                  Design = val!;
                                });
                              }),
                              SizedBox(width: 10,),
                              Text("Design",style: AppTextStyle.helpReq,)
                            ],
                          ),
                        ),
                        
                        SizedBox(height: 5,),
                                
                                
                        Padding(
                          padding: const EdgeInsets.only(left: 20,right: 20),
                          child: Row(
                            children: [
                              Checkbox(activeColor: AppColors.primary,
                                value: Technical, onChanged: (val){
                                setState(() {
                                  Technical = val!;
                                });
                              }),
                              SizedBox(width: 10,),
                              Text("Technical",style: AppTextStyle.helpReq,)
                            ],
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
                
                Padding(
                 padding: const EdgeInsets.only(left: 20,right: 20,bottom: 20,top: 18),
                 child: Text('Availability and Preferred Time:',style: AppTextStyle.helpReq,),
                           ),
                           
      
      Padding(
                  padding: const EdgeInsets.only(left: 20,right: 20,bottom: 20,),
                  child: Text('Preferred Days:',style: AppTextStyle.helpReq,),
                ),
                Row(
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Padding(
                          padding: const EdgeInsets.only(left: 20,right: 20),
                          child: Row(
                            children: [
                              Checkbox(activeColor: AppColors.primary,
                                value: Sunday, onChanged: (val){
                                setState(() {
                                  Sunday = val!;
                                });
                              }),
                              SizedBox(width: 10,),
                              Text("Sunday",style: AppTextStyle.helpReq,)
                            ],
                          ),
                        ),
                        SizedBox(height: 5,),
                                
                                
                        Padding(
                          padding: const EdgeInsets.only(left: 20,right: 20),
                          child: Row(
                            children: [
                              Checkbox(activeColor: AppColors.primary,
                                value: Monday, onChanged: (val){
                                setState(() {
                                  Monday = val!;
                                });
                              }),
                              SizedBox(width: 10,),
                              Text("Monday",style: AppTextStyle.helpReq,)
                            ],
                          ),
                        ),
                                
                                
                                
                        SizedBox(height: 5,),
                                
                                
                        Padding(
                          padding: const EdgeInsets.only(left: 20,right: 20),
                          child: Row(
                            children: [
                              Checkbox(activeColor: AppColors.primary,
                                value: Tuesday, onChanged: (val){
                                setState(() {
                                  Tuesday = val!;
                                });
                              }),
                              SizedBox(width: 10,),
                              Text("Tuesday",style: AppTextStyle.helpReq,)
                            ],
                          ),
                        ),
                                
                                
                                
                                
                                
                        SizedBox(height: 5,),
                                
                                
                        Padding(
                          padding: const EdgeInsets.only(left: 20,right: 20),
                          child: Row(
                            children: [
                              Checkbox(activeColor: AppColors.primary,
                                value: Wednisdey, onChanged: (val){
                                setState(() {
                                  Wednisdey = val!;
                                });
                              }),
                              SizedBox(width: 10,),
                              Text("Wednisday",style: AppTextStyle.helpReq,)
                            ],
                          ),
                        ),
                      ],
                    ),
                            
                            
                            
                    SizedBox(height: 5,),
                            
                            
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisAlignment: MainAxisAlignment.start,
                      children: [
                        Padding(
                          padding: const EdgeInsets.only(left: 20,right: 20),
                          child: Row(
                            children: [
                              Checkbox(activeColor: AppColors.primary,
                                value: Thursday, onChanged: (val){
                                setState(() {
                                  Thursday = val!;
                                });
                              }),
                              SizedBox(width: 10,),
                              Text("Thursday",style: AppTextStyle.helpReq,)
                            ],
                          ),
                        ),
                        
                        SizedBox(height: 5,),
                                
                                
                        Padding(
                          padding: const EdgeInsets.only(left: 20,right: 20),
                          child: Row(
                            children: [
                              Checkbox(activeColor: AppColors.primary,
                                value: Friday, onChanged: (val){
                                setState(() {
                                  Friday = val!;
                                });
                              }),
                              SizedBox(width: 10,),
                              Text("Friday",style: AppTextStyle.helpReq,)
                            ],
                          ),
                        ),
                        
                        SizedBox(height: 5,),
                                
                                
                        Padding(
                          padding: const EdgeInsets.only(left: 20,right: 20),
                          child: Row(
                            children: [
                              Checkbox(activeColor: AppColors.primary,
                                value: Saturday, onChanged: (val){
                                setState(() {
                                  Saturday = val!;
                                });
                              }),
                              SizedBox(width: 10,),
                              Text("Saturday",style: AppTextStyle.helpReq,)
                            ],
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
                
      
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Padding(
                    padding: const EdgeInsets.only(top:10,left: 20,right: 20),
                    child: Text('Preferred Time:',style: AppTextStyle.helpReq,),
                              ),
                    Row(
                    children: [
                        
                              Padding(
                    padding: const EdgeInsets.only(top:7),
                    child: Padding(
                      padding: const EdgeInsets.only(left:10),
                      child: Row(
                        children: [
                          Radio(activeColor: AppColors.primary,
                            value: "Morning", groupValue: Time, onChanged: (val)=>setState(() {
                            Time =val as String;
                          })),
                          Text("Morning")
                        ],
                      ),
                    ),
                              ),
                    
                    Padding(
                      padding:const EdgeInsets.only(top:7),
                      child: Row(
                        children: [
                          Radio(activeColor: AppColors.primary,
                            value: "Evening", groupValue: Time, onChanged: (val)=>setState(() {
                            Time =val as String;
                          })
                          ),
                          Text("Evening")
                        ],
                      ),
                    ),
                    
                       Padding(
                      padding:const EdgeInsets.only(top:7),
                      child: Row(
                        children: [
                          Radio(activeColor: AppColors.primary,
                            value: "All Day", groupValue: Time, onChanged: (val)=>setState(() {
                            Time =val as String;
                          })
                          ),
                          Text("All Day")
                        ],
                      ),
                    ),
                    
                    ]
                    ),
          ],
        ),
                
       Column(
         children: [
       Row(
                    children: [
                        Padding(
                    padding: const EdgeInsets.only(top:5,left: 20,),
                    child: Text('Previous Volunteering Experience: ',style: AppTextStyle.helpReq,),
                              ),
                              Padding(
                    padding: const EdgeInsets.only(top:7),
                    child: Row(
                      children: [
                        Radio(activeColor: AppColors.primary,
                          value: "True", groupValue: experience, onChanged: (val)=>setState(() {
                          experience =val as String;
                        })),
                        Text("Yes")
                      ],
                    ),
                              ),
       
       
                    
                    Padding(
                      padding:const EdgeInsets.only(top:7),
                      child: Row(
                        children: [
                          Radio(activeColor: AppColors.primary,
                            value: "false", groupValue: experience, onChanged: (val)=>setState(() {
                            experience =val as String;
                          })
                          ),
                          Text("No")
                        ],
                      ),
                    ),
                    
                    
                    ]
                    ),
      
      if (experience == "True")
                Padding(
                  padding: const EdgeInsets.only(left: 20,right: 20,top: 20),
                  child: TextFormField(
                    controller: experiencedetails,
                    maxLines: 2,
                    keyboardType: TextInputType.number,
                    decoration: AppInputDecoration.defaultDecoration.copyWith(
                      labelText: "Please mention your experience or organizations:",
                  
                    ),
                    validator: (value) {
                      if (value == null || value.isEmpty) {
                         return "Please mention your experience or organizations";
                      }
                    },
                  ),
                ),
                    
         ],
       ),
      
                
                Column(
                 crossAxisAlignment: CrossAxisAlignment.start,
                 children: [
                    Padding(
               padding: const EdgeInsets.only(left: 20,right: 20,bottom: 10,top: 20),
               child: Text('phone Number :',style: AppTextStyle.helpReq,),
                         ),
                         
                         Padding(
               padding: const EdgeInsets.only(left: 20,right: 20,bottom: 20,top: 4),
               child: Text('please make sure of the phone number ehere\nyou will be connected',style: AppTextStyle.helpReq,),
                         ), 
                   Padding(
                     padding:  const EdgeInsets.only(left: 20,right: 20),
                     child: TextFormField(
                      
                      controller: phoneNumber,
                     keyboardType: TextInputType.number,
                       decoration: AppInputDecoration.defaultDecoration.copyWith(
                         label: Text("Your phone number"),
                        prefixIcon:
                          
                            Icon(Icons.phone),
                            prefix :Text('+963')
                          
                        
                      
                         
                       ),
                       validator: (value) {
                         if (value == null || value.isEmpty) {
                           return 'please enter your phone Number';
                         }
                         else if (value.length != 9){
                          return 'it must be 9 numbers';
                         }
                         else if (!RegExp(r'^\d{9}$').hasMatch(value)){
                          return 'Only digits are allowed';
                         }
                          return null;
                       },
                     ),
                   ),
          
          
                 ],
               ),
       Column(
                   crossAxisAlignment: CrossAxisAlignment.start,
                   children: [
                      Padding(
                 padding: const EdgeInsets.only(left: 20,right: 20,bottom: 20,top: 20),
                 child: Text('Additional Notes or Details:',style: AppTextStyle.helpReq,),
                           ),
                     Padding(
                       padding:  const EdgeInsets.only(left: 20,right: 20),
                       child: TextFormField(
                        controller: details,
                        maxLines: 2,
                       keyboardType: TextInputType.text,
                         decoration: AppInputDecoration.defaultDecoration.copyWith(
                         
                           
                         ),
                        //  validator: (value) {
                        //    if (value == null || value.isEmpty) {
                        //      return 'please enter your address';
                        //    }
                        //  },
                       ),
                     ),
                   ],
                 ),
                 SizedBox(height: 10,),
      Padding(
                          padding: const EdgeInsets.only(left: 20,right: 20),
                          child: Row(
                            children: [
                              Checkbox(activeColor: AppColors.primary,
                                value: Agree, onChanged: (val){
                                setState(() {
                                  Agree = val!;
                                });
                              }),
                              SizedBox(width: 10,),
                              Text("I agree to the terms and conditions of\nvolunteering at the association.",style: AppTextStyle.helpReq,)
                            ],
                          ),
                        ),
      
      
      Padding(
                 padding:  EdgeInsets.only(left: 250,right: 20,top: 20),
                 child: ElevatedButton(onPressed: (){
                  
                  submitForm();
                 }, child: Text('Next'),
                 style: ElevatedButton.styleFrom(
                   backgroundColor: AppColors.primary,
                   fixedSize: Size(100, 40),
                   foregroundColor: AppColors.white
                 ),
                 ),
               )
      
      
                    ],
                  )
                  ,
                ),
              )
            ],
          ),
        ),
      ),
    );
  }
}