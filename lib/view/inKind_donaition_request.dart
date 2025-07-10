import 'package:charity_project/app_colors.dart';
import 'package:charity_project/view/app_text_style.dart';
import 'package:charity_project/view/background.dart';
import 'package:charity_project/view/charity_fund_page.dart';
import 'package:charity_project/view/input_decoraition.dart';
import 'package:flutter/material.dart';

class InkindDonaitionRequest extends StatefulWidget {
  const InkindDonaitionRequest({super.key});

  @override
  State<InkindDonaitionRequest> createState() => _InkindDonaitionRequestState();
}

class _InkindDonaitionRequestState extends State<InkindDonaitionRequest> {
  final formkey = GlobalKey<FormState>();
  TextEditingController name = TextEditingController();
  TextEditingController address = TextEditingController();
  TextEditingController phoneNumber = TextEditingController();
bool childrenToys = false;
bool furniture = false;
bool electronics = false;
bool clothes = false;
bool others = false;

void submitForm (){
  if (formkey.currentState!.validate()) {
   bool atleastone = childrenToys || furniture || electronics || clothes || others ;
   if ( !atleastone ) {
     ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text("You have to select one of donaition kinds")));
     return ;
   }
     Navigator.push(context, MaterialPageRoute(builder: (context)=>CharityFundPage()));
  }
 
}
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: BackgroundWrapper(child: Form(
        key: formkey,
        child: SingleChildScrollView(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              AppBar(
                backgroundColor: AppColors.white,
                title: Text('New Request',style: AppTextStyle.a,),
                leading: IconButton(onPressed: (){
                  Navigator.pop(context);
                }, icon: Icon(Icons.arrow_back)),
              ),
              
              Padding(
                padding: const EdgeInsets.only(left: 20,right: 20),
                child: TextFormField(controller: name,
                keyboardType: TextInputType.text,
                  decoration: AppInputDecoration.defaultDecoration.copyWith(
                    label: Text('Your name'),
                    
                  ),
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'please enter your name';
                    }
                  },
                ),
              ),
            Padding(
                padding: const EdgeInsets.only(left: 20,right: 20,bottom: 20,top: 20),
                child: Text('Select Donaition Kinds',style: AppTextStyle.helpReq,),
              ),
              Padding(
                padding: const EdgeInsets.only(left: 20,right: 20),
                child: Row(
                  children: [
                    Checkbox(activeColor: AppColors.primary,
                      value: childrenToys, onChanged: (val){
                      setState(() {
                        childrenToys = val!;
                      });
                    }),
                    SizedBox(width: 10,),
                    Text("Children Toys",style: AppTextStyle.helpReq,)
                  ],
                ),
              ),
              SizedBox(height: 5,),
          
          
              Padding(
                padding: const EdgeInsets.only(left: 20,right: 20),
                child: Row(
                  children: [
                    Checkbox(activeColor: AppColors.primary,
                      value: furniture, onChanged: (val){
                      setState(() {
                        furniture = val!;
                      });
                    }),
                    SizedBox(width: 10,),
                    Text("Furniture",style: AppTextStyle.helpReq,)
                  ],
                ),
              ),
          
          
          
              SizedBox(height: 5,),
          
          
              Padding(
                padding: const EdgeInsets.only(left: 20,right: 20),
                child: Row(
                  children: [
                    Checkbox(activeColor: AppColors.primary,
                      value: electronics, onChanged: (val){
                      setState(() {
                        electronics = val!;
                      });
                    }),
                    SizedBox(width: 10,),
                    Text("Electronics",style: AppTextStyle.helpReq,)
                  ],
                ),
              ),
          
          
          
          
          
              SizedBox(height: 5,),
          
          
              Padding(
                padding: const EdgeInsets.only(left: 20,right: 20),
                child: Row(
                  children: [
                    Checkbox(activeColor: AppColors.primary,
                      value: clothes, onChanged: (val){
                      setState(() {
                        clothes = val!;
                      });
                    }),
                    SizedBox(width: 10,),
                    Text("Clothes",style: AppTextStyle.helpReq,)
                  ],
                ),
              ),
          
          
          
              SizedBox(height: 5,),
          
          
              Padding(
                padding: const EdgeInsets.only(left: 20,right: 20),
                child: Row(
                  children: [
                    Checkbox(activeColor: AppColors.primary,
                      value: others, onChanged: (val){
                      setState(() {
                        others = val!;
                      });
                    }),
                    SizedBox(width: 10,),
                    Text("Others",style: AppTextStyle.helpReq,)
                  ],
                ),
              ),
               Column(
                 crossAxisAlignment: CrossAxisAlignment.start,
                 children: [
                    Padding(
               padding: const EdgeInsets.only(left: 20,right: 20,bottom: 20,top: 20),
               child: Text('Donaition collection address',style: AppTextStyle.helpReq,),
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
               padding: const EdgeInsets.only(left: 20,right: 20,bottom: 10,top: 20),
               child: Text('phone Number',style: AppTextStyle.helpReq,),
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
        ),
      )),
    );
  }
}