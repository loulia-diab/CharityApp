import 'package:charity_project/app_colors.dart';
import 'package:charity_project/view/background.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

class PeriodicallyDonaition extends StatefulWidget {
  const PeriodicallyDonaition({super.key});

  @override
  State<PeriodicallyDonaition> createState() => _PeriodicallyDonaitionState();
}

class _PeriodicallyDonaitionState extends State<PeriodicallyDonaition> {

  final List <int> amounts = [10,20,50,100];
  final List <String> periodically = ['Daily','Weekly','Monthly'];
  int? selectedamount;
  String? selectedperiodically;
  final _formkey = GlobalKey<FormState>();
  TextEditingController amountin = TextEditingController();


  void updateAmount ( int amount){
    setState(() {
      selectedamount = amount;
      amountin.text = amount.toString();
    });
   
  }

  void updatePeriod(String period) {
    setState(() {
      selectedperiodically = period;
    });
  }
 void onTextChanged(String value) {
    final entered = int.tryParse(value);
    setState(() {
      selectedamount = (entered != null && entered < 1000) ? entered : null;
    });
  }

  bool get isValid => selectedamount!=null && selectedperiodically != null;
  @override
  Widget build(BuildContext context) {
    final displayamount = selectedamount ?? 0 ;
    // final displayperiodically = selectedperiodically ?? "";
    return Scaffold(
      backgroundColor: AppColors.background,
      body: BackgroundWrapper(
        child: Form(
          key: _formkey,
          child: Column(
            children: [
              AppBar(
                backgroundColor: AppColors.white,
                leading: IconButton(onPressed: (){
                  Navigator.pop(context);
                }, icon: Icon(Icons.arrow_back)),
              ),
              Padding(
                padding: const EdgeInsets.only(top:20),
                child: Text('Periodically Donaition',style: TextStyle(color: AppColors.primary,fontSize: 20,fontWeight: FontWeight.w700),),
              ),
              Padding(
                padding: const EdgeInsets.only(top:20),
                child: Container(
                  height: 100,width: 150,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(12),
                    color: Color(0xff32bfa0).withOpacity(0.5),
                    
                  ),
                  child: Image.asset('assets/images/mmm.png',height: 100,),
          
                ),
              ),
      
          
      Padding(
             padding: const EdgeInsets.only(top:20,left: 10,right: 10),
             child: 
             Row(
               mainAxisAlignment: MainAxisAlignment.spaceBetween,
               children: periodically.map((period) {
                 final isSelected = selectedperiodically == period;
                 return Expanded(
                   child: Padding(
                     padding: const EdgeInsets.symmetric(horizontal: 4),
                     child: GestureDetector(
              onTap: () => updatePeriod(period),
              child: Container(
                height: 50,width: 70,
                padding: EdgeInsets.symmetric(vertical: 14),
                decoration: BoxDecoration(
                  color:  isSelected ? AppColors.primary : AppColors.white,
                  border: Border.all(color: AppColors.primary),
                  borderRadius: BorderRadius.circular(20),
                ),
                alignment: Alignment.center,
                child: Center(
                  child: Text(
                    "$period ",
                    style: TextStyle(
                      color: isSelected ? Colors.white : AppColors.primary,
                      fontSize: 16,
                    ),
                  ),
                ),
              ),
                     ),
                   ),
                 );
               }).toList(),
             ),
           )
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
           ,Padding(
             padding: const EdgeInsets.only(top:20,left: 10,right: 10),
             child: Row(
               mainAxisAlignment: MainAxisAlignment.spaceBetween,
               children: amounts.map((amount) {
                 final isSelected = selectedamount == amount;
                 return Expanded(
                   child: Padding(
                     padding: const EdgeInsets.symmetric(horizontal: 4),
                     child: GestureDetector(
              onTap: () => updateAmount(amount),
              child: Container(
                height: 50,width: 70,
                padding: EdgeInsets.symmetric(vertical: 14),
                decoration: BoxDecoration(
                  color:  isSelected ? AppColors.primary : AppColors.white,
                  border: Border.all(color: AppColors.primary),
                  borderRadius: BorderRadius.circular(20),
                ),
                alignment: Alignment.center,
                child: Text(
                  "$amount \$",
                  style: TextStyle(
                    color: isSelected ? Colors.white : AppColors.primary,
                    fontSize: 16,
                  ),
                ),
              ),
                     ),
                   ),
                 );
               }).toList(),
             ),
           ),
      
      
      
      
           Padding(
             padding: const EdgeInsets.only(top:20,left:10,right: 10),
             child: TextFormField(
              cursorColor: AppColors.primary,
              
              controller: amountin,
              keyboardType: TextInputType.number,
              onChanged: onTextChanged,
              validator: (value) {
                int val = int.tryParse(value ?? '') ?? 0 ;
                if (val > 1000) return "Amount must be less than 1000";
                    if (val <= 0) return "Please enter a valid amount";
                    return null;
              },decoration: InputDecoration(
                labelStyle: TextStyle(color: AppColors.primary),
                labelText: "anouther amount",
                suffix: Text('\$'),
                
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(20),
                  borderSide: BorderSide(color: AppColors.primary)
                )
              , border: OutlineInputBorder(
                borderRadius: BorderRadius.circular(20)
              )
              
              ),
             ),
           ),
           SizedBox(height: 30,),
           Row(
            children: [
            Padding(
              padding: const EdgeInsets.only(left:20,right: 20),
              child: Text('Total Amount :',style: TextStyle(
                color: AppColors.black,fontSize: 20,fontWeight: FontWeight.w700,
              ),),
            ),
      
            Text(isValid ? "$displayamount \$ ($selectedperiodically)":"__",
            style: TextStyle(color: AppColors.primary,fontSize: 20,fontWeight: FontWeight.w700,),)
           ],),
      
           SizedBox(height: 40,),
           Column(children: [
      
       ElevatedButton(
                      onPressed: isValid
                          ? () {
                              if (_formkey.currentState!.validate()) {
                                final amount = amountin.text;
                                ScaffoldMessenger.of(context).showSnackBar(
                                  SnackBar(
                                      content: Text(
                                          "Paid $amount \$ (${selectedperiodically!})")),
                                );
                              }
                            }
                          : null,
                      child: Row(
                        children: [
                          Icon(Icons.payment,color: AppColors.white,size: 30,),
                           SizedBox(width: 33,),
                          Text("Pay Now",style: TextStyle(fontSize: 16),),
                        ],
                      ),
                      style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.primary,
                foregroundColor: AppColors.white,
                fixedSize: Size(250, 50)
              ),
              
                    ),
      
      
      
      
      
      
      
      SizedBox(height: 20,) ,
      
      
      
      
      
            ElevatedButton(
              onPressed: isValid
                  ? () {
                      if (_formkey.currentState!.validate()) {
                        final amount = amountin.text;
                        ScaffoldMessenger.of(context).showSnackBar(
                          SnackBar(
                              content: Text(
                                  "Added $amount \$ to cart (${selectedperiodically!})")),
                        );
                      }
                    }
                  : null,
              child: Row(
                children: [
                  Image.asset('assets/images/fund.png',color: AppColors.primary,height: 30,),
                  SizedBox(width: 30,),
                  Text("Add to Cart",style: TextStyle(fontSize: 16),),
                ],
              ),
              style: ElevatedButton.styleFrom(
                shape: RoundedRectangleBorder(
      side: BorderSide(color: AppColors.primary,width: 2),
                  borderRadius: BorderRadius.circular(25)
                ),
                backgroundColor: AppColors.white,
                foregroundColor: AppColors.primary,
                fixedSize: Size(250, 50)
              ),
              
            ),
                 
      
      
           ],)
      
            ],
          ),
        ),
      ),
    );
  }
}
