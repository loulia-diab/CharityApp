import 'package:charity_project/app_colors.dart';
import 'package:charity_project/view/app_text_style.dart';
import 'package:charity_project/view/background.dart';
import 'package:charity_project/view/charity_fund_page.dart';
import 'package:charity_project/view/input_decoraition.dart';
import 'package:flutter/material.dart';

class RequestHelpPage extends StatefulWidget {
   RequestHelpPage({super.key});

  @override
  State<RequestHelpPage> createState() => _RequestHelpPageState();
}

class _RequestHelpPageState extends State<RequestHelpPage> {
   final _formKey = GlobalKey<FormState>();
String? employmentStatus;
String? LivingStatus;
String? MaritialStatus;
String? selectedStudy ;
String? selectedHelpType ;

List<String> Study =["primary","preperatory","Secondary","acadimic","Not Study"];
String? hasIncome ;
DateTime? selectedDate ;
   final TextEditingController firstname = TextEditingController();
      final TextEditingController lastname = TextEditingController();
         final TextEditingController fathername = TextEditingController();
               final TextEditingController mothername = TextEditingController();
                 final TextEditingController address = TextEditingController();
                   final TextEditingController phoneNumber = TextEditingController();
final TextEditingController incomeController = TextEditingController();
   final TextEditingController birthDateController = TextEditingController();

final TextEditingController numberoffamilymember = TextEditingController();
final TextEditingController details = TextEditingController();
void pickBirthDate() async {
  DateTime? pickedDate = await showDatePicker(
    context: context,
    initialDate: DateTime.now(),
    firstDate: DateTime(1900),
    lastDate: DateTime.now(),

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
      birthDateController.text =
          "${pickedDate.day}/${pickedDate.month}/${pickedDate.year}";
    });
  }
}

void submitForm(){
  if (_formKey.currentState!.validate()) {
    Navigator.push(context, MaterialPageRoute(builder: (context)=>CharityFundPage()));
  }
}



  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: 
       AppBar(
              
      backgroundColor: AppColors.white,
      title: Text('Help Request ',style: TextStyle(fontSize: 20,fontWeight: FontWeight.w600,color: AppColors.primary),),
      
        ),
      body: BackgroundWrapper(
        child: Column(
          children: [
           
        SizedBox(
      height: 700,
      child: ListView(
        scrollDirection: Axis.vertical,
        children: [
          Form(
        key: _formKey,
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
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Padding(
                    padding: const EdgeInsets.only(top:5,left: 20,right: 20),
                    child: Text('Mother Name',style: AppTextStyle.helpReq,),
                  ),
                  Padding(
                    padding: const EdgeInsets.all(8.0),
                    child: Expanded(
                      child: TextFormField(
                        cursorColor: AppColors.primary,
                        controller: mothername,
                        decoration: AppInputDecoration.defaultDecoration.copyWith(
                                  
                        ),
                        validator: (value) {
                          if (value == null || value.isEmpty) {
                            return 'please enter your full name';
                          }
                          return null;
                        },
                      
                      ),
                    ),
                  ),
              ],
            ),
          ),
      
      SizedBox(width: 10,),
      
      Expanded(
        child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
                padding: const EdgeInsets.only(top:5,left: 20,right: 20),
                child: Text('Father Name',style: AppTextStyle.helpReq,),
              ),
              Padding(
                padding: const EdgeInsets.all(8.0),
                child: Expanded(
                  child: TextFormField(
                    cursorColor: AppColors.primary,
                    controller: fathername,
                    decoration: AppInputDecoration.defaultDecoration.copyWith(
                      
                    ),
                    validator: (value) {
                      if (value == null || value.isEmpty) {
                        return 'please enter your full name';
                      }
                      return null;
                    },
                  
                  ),
                ),
              ),
      ],
        ),
      ),
      
        ],
      ),
           
       Padding(
          padding: const EdgeInsets.only(top:5,left: 20,right: 20),
          child: Text('BirthDate',style: AppTextStyle.helpReq,),
        ),
        Padding(
          padding: const EdgeInsets.all(8.0),
          child: TextFormField(
            onTap: pickBirthDate,
            cursorColor: AppColors.primary,
            
            controller: birthDateController,
            readOnly: true,
            
            decoration: AppInputDecoration.defaultDecoration.copyWith(
              prefixIcon: Icon(Icons.date_range),
      label: Text("Birthdate")
            ),
            validator: (value) {
              if (selectedDate == null) {
                return 'please enter your Date';
              }
              return null;
            },
          
          ),
        ),
        SizedBox(height: 6),
      
      
      Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
      Padding(
              padding: const EdgeInsets.only(top:5,left: 20,right: 5),
              child: Text('Maritial Status: ',style: AppTextStyle.helpReq,),
            ),
            Padding(
              padding:  const EdgeInsets.only(top:5,left: 20,right: 20),
              child: Row(
                children: [
                  Row(
                    children: [
                      Radio(activeColor: AppColors.primary,
                        value: "Single", groupValue: MaritialStatus, onChanged: (val)=>setState(() {
                        MaritialStatus =val as String;
                      })),
                      Text("Single")
                    ],
                  ),
                  
                  Row(
                    children: [
                      Radio(activeColor: AppColors.primary,
                        value: "married", groupValue: MaritialStatus, onChanged: (val)=>setState(() {
                        MaritialStatus =val as String;
                      })
                      ),
                      Text("married")
                    ],
                  ),
                  
                  
                  Row(
                    children: [
                      Radio(activeColor: AppColors.primary,
                        value: "widower", groupValue: MaritialStatus, onChanged: (val)=>setState(() {
                        MaritialStatus =val as String;
                      })
                      ),
                      Text("widower")
                    ],
                  ),
                ],
              ),
            ),
        ],
      ),
      Padding(
                padding: const EdgeInsets.only(top:5,left: 10,right: 10),
                child: Text('Number of family member :',style: AppTextStyle.helpReq,),
              ),
              Padding(
                padding:   const EdgeInsets.only(top:5,left: 10,right: 10),
                child: Expanded(
                  child: TextFormField(
                    cursorColor: AppColors.primary,
                    controller: numberoffamilymember,
                    decoration: AppInputDecoration.defaultDecoration.copyWith(
                      
                    ),
                     validator: (value) {
                        if (value == null || value.isEmpty) {
                          return 'Please enter the number of family members';
                        }
                        final number = int.tryParse(value);
                        if (number == null) {
                            return 'Please enter a valid number';
                        }
                        if (number <=0 || number >= 30) {
                            return 'Please enter a realistic number';
                        }
                        return null;
                      },
                  
                  ),
                ),
              ),
        Padding(
          padding:  const EdgeInsets.only(top:20,left: 10,right: 10),
          child: DropdownButtonFormField(value: selectedStudy,
                decoration: AppInputDecoration.defaultDecoration.copyWith(
          label: Text("Study")
                ),
          items:  Study.map((studytype) {
                      return DropdownMenuItem(value: studytype, child: Text(studytype));
                    }).toList(), onChanged: (value)=>setState(() {
          selectedStudy = value;
                })),
        ),
      
      Padding(
      padding:  const EdgeInsets.only(top:10,left: 10,right: 10),
        child: Row(
          children: [
        Padding(
                padding: const EdgeInsets.only(top:5,left: 20,right: 20),
                child: Text('Do You Have Job ?',style: AppTextStyle.helpReq,),
              ),
              Row(
                children: [
                  Radio(value: "yes", groupValue: employmentStatus, onChanged: (val)=>setState(() {
                    employmentStatus =val as String;
                  }),activeColor: AppColors.primary,
        
                  ),
                  Text("Yes")
                ],
              ),
        
        Row(
          children: [
        Radio(activeColor: AppColors.primary,
          value: "No", groupValue: employmentStatus, onChanged: (val)=>setState(() {
          employmentStatus =val as String;
        })
        ),
        Text("No")
          ],
        ),
          ],
        ),
      ),
      
      
      
      
      Row(
        children: [
      Padding(
              padding: const EdgeInsets.only(top:5,left: 20,right: 20),
              child: Text('Living : ',style: AppTextStyle.helpReq,),
            ),
            Row(
              children: [
                Radio(activeColor: AppColors.primary,
                  value: "yes", groupValue: LivingStatus, onChanged: (val)=>setState(() {
                  LivingStatus =val as String;
                })),
                Text("Rent")
              ],
            ),
      
      Row(
        children: [
      Radio(activeColor: AppColors.primary,
        value: "No", groupValue: LivingStatus, onChanged: (val)=>setState(() {
        LivingStatus =val as String;
      })
      ),
      Text("own")
        ],
      ),
        ],
      ),
      
      
      
    
      Row(
        children: [
      Padding(
              padding: const EdgeInsets.only(top:5,left: 20,right: 20),
              child: Text('Do you have fixed income ',style: AppTextStyle.helpReq,),
            ),
            Row(
              children: [
                Radio(activeColor: AppColors.primary,
                  value: "yes", groupValue: hasIncome, onChanged: (val)=>setState(() {
                  hasIncome = val;
                })),
                Text("Yes")
              ],
            ),
      
      Row(
        children: [
      Radio(activeColor: AppColors.primary,
        value: "No", groupValue: hasIncome, onChanged: (val)=>setState(() {
        hasIncome = val;
      })
      ),
      Text("No")
        ],
      ),
        ],
      ),
      
      if (hasIncome == "yes")
                Padding(
                  padding: const EdgeInsets.only(left: 20,right: 20,bottom: 20,top: 20),
                  child: TextFormField(
                    controller: incomeController,
                    keyboardType: TextInputType.number,
                    decoration: AppInputDecoration.defaultDecoration.copyWith(
                      labelText: "Monthly Income",
                      
                      
                    ),
                  ),
                ),
      
      Column(
                 crossAxisAlignment: CrossAxisAlignment.start,
                 children: [
                    Padding(
               padding: const EdgeInsets.only(left: 20,right: 20,bottom: 20,top: 5),
               child: Text('Your Address',style: AppTextStyle.helpReq,),
                         ),
                   Padding(
                     padding:  const EdgeInsets.only(left: 20,right: 20),
                     child: TextFormField(
                      controller: address,
                      maxLines: 2,
                     keyboardType: TextInputType.text,
                       decoration: AppInputDecoration.defaultDecoration.copyWith(
                       
                         
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
               padding: const EdgeInsets.only(left: 20,right: 20,bottom: 10,top: 20),
               child: Text('phone Number',style: AppTextStyle.helpReq,),
                         ),
                         
                         Padding(
               padding: const EdgeInsets.only(left: 20,right: 20,bottom: 20,top: 4),
               child: Text('please make sure of the phone number\nwehere you will be connected',style: AppTextStyle.helpReq,),
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

      Padding(
        padding:  const EdgeInsets.only(top:20,left: 10,right: 10),
        child: DropdownButtonFormField<String>(
          decoration: AppInputDecoration.defaultDecoration.copyWith(
            label: Text("Select the type of help")
          ),
          value: selectedHelpType,
          items: [
        // العنوان 1: الكفالة (غير قابل للاختيار)
        const DropdownMenuItem<String>(
          child: Text(
            '--- الكفالة ---',
            style: TextStyle(fontWeight: FontWeight.bold, color: AppColors.primary),
          ),
          enabled: false,
        ),
        const DropdownMenuItem(value: 'طالب علم', child: Text('طالب علم')),
        const DropdownMenuItem(value: 'يتيم', child: Text('يتيم')),
        const DropdownMenuItem(value: 'ذوي احتياجات', child: Text('ذوي احتياجات')),
        const DropdownMenuItem(value: 'أسرة', child: Text('أسرة')),
        
        // العنوان 2: المساعدة الإنسانية
        const DropdownMenuItem<String>(
          child: Text(
            '--- المساعدة الإنسانية ---',
            style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey),
          ),
          enabled: false,
        ),
        const DropdownMenuItem(value: 'تعليم', child: Text('تعليم')),
        const DropdownMenuItem(value: 'صحة', child: Text('صحة')),
        const DropdownMenuItem(value: 'غذاء', child: Text('غذاء')),
        const DropdownMenuItem(value: 'مياه', child: Text('مياه')),
        const DropdownMenuItem(value: 'غارم', child: Text('غارم')),
        
        // العنوان 3: التبرعات العينية
        const DropdownMenuItem<String>(
          child: Text(
            '--- التبرعات العينية ---',
            style: TextStyle(fontWeight: FontWeight.bold, color: Colors.grey),
          ),
          enabled: false,
        ),
        const DropdownMenuItem(value: 'لعب أطفال', child: Text('لعب أطفال')),
        const DropdownMenuItem(value: 'عفش', child: Text('عفش')),
        const DropdownMenuItem(value: 'ملابس', child: Text('ملابس')),
        const DropdownMenuItem(value: 'أجهزة إلكترونية', child: Text('أجهزة إلكترونية')),
          ],
          onChanged: (value) {
        setState(() {
          selectedHelpType = value;
        });
          },
          validator: (value) =>
          value == null ? 'يرجى اختيار نوع المساعدة' : null,
        ),
      )
      
      ,
      Column(
                 crossAxisAlignment: CrossAxisAlignment.start,
                 children: [
                    Padding(
               padding: const EdgeInsets.only(left: 20,right: 20,bottom: 20,top: 5),
               child: Text('Write a simple explenaition about your ststus ',style: AppTextStyle.helpReq,),
                         ),
                   Padding(
                     padding:  const EdgeInsets.only(left: 20,right: 20),
                     child: TextFormField(
                      controller: details,
                      maxLines: 2,
                     
                       decoration: AppInputDecoration.defaultDecoration.copyWith(
                       
                         
                       ),
                       validator: (value) {
                         if (value == null || value.isEmpty) {
                           return 'please write a simple explenaition about your ststus';
                         }
                       },
                     ),
                   ),
                 ],
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
        ),
      )
        ],
      ),
        )
          ],
      
        ),
      ),
    ) ;
  }
}