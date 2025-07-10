import 'package:charity_project/app_colors.dart';
import 'package:charity_project/view/app_text_style.dart';
import 'package:charity_project/view/background.dart';
import 'package:charity_project/view/donation_categories_page.dart';
import 'package:charity_project/view/input_decoraition.dart';
import 'package:flutter/material.dart';
List<String>paymethods = ["credit card","paypal","cash"];
String? paymethodsselected;
class PayDetailsPage extends StatefulWidget {
  const PayDetailsPage({super.key});

  @override
  State<PayDetailsPage> createState() => _PayDetailsPageState();
}

class _PayDetailsPageState extends State<PayDetailsPage> {
  @override
  void initState() {
  super.initState();
  paymethodsselected ??= paymethods.first; 
}
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      appBar: AppBar(
        backgroundColor: AppColors.white,
        title: Text("Checkout",style: AppTextStyle.a,),

      ),
      body: BackgroundWrapper(child: Column(
        children: [
          SizedBox(height: 10,),
          Text("Donation Details ",style: AppTextStyle.a,),
          SizedBox(height: 20,),
          Image.asset("assets/images/pay1.png",height: 100,),
          SizedBox(height: 20,),
          Padding(
            padding: const EdgeInsets.all(8.0),
            child: Row(
              children: [
                Text("Donation For :",style: AppTextStyle.a,),
                SizedBox(width: 100,),
                Text("campaign",style: AppTextStyle.helpReq,)
              ],
            ),
          ),
          Divider(endIndent: 10,indent: 10,),
          Padding(
            padding: const EdgeInsets.all(8.0),
            child: Row(
              children: [
                Text("Donation Type :",style: AppTextStyle.a,),
                SizedBox(width: 100,),
                Text("once",style: AppTextStyle.helpReq,)
              ],
            ),
          ),
          Divider(endIndent: 10,indent: 10,),
            Padding(
              padding: const EdgeInsets.all(8.0),
              child: Row(
              children: [
                Text("Amount :",style: AppTextStyle.a,),
                SizedBox(width: 155,),
                Text("\$ 300",style: AppTextStyle.helpReq,)
              ],
                        ),
            ),
            Divider(endIndent: 10,indent: 10,),

            Padding(
              padding: const EdgeInsets.all(8.0),
              child: Row(
              children: [
                Text("Payment Method :",style: AppTextStyle.a,),
                SizedBox(width: 10,),
                Expanded(
                  child: DropdownButtonFormField(
                    decoration: AppInputDecoration.defaultDecoration.copyWith(
                    
                  ),
                   value: paymethodsselected,items: paymethods.map((payType){
                  return DropdownMenuItem(child: Text(payType),value: payType,);
                  }).toList()
                  , onChanged: (value)=>setState(() {
                    paymethodsselected = value;
                  })),
                ),

      
              ],
                        ),
            ),
            SizedBox(height: 50,),
                 ElevatedButton(

onPressed: () {
    showDialog(
      context: context,
      builder: (context) => Dialog(
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
        ),
        child: Container(
          width: 500,
          height: 400,
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(20),
          ),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              
              const Text(
                "Thank You For Your Donation !!",
                textAlign: TextAlign.center,
                style: AppTextStyle.a
              ),
              Image.asset("assets/images/pay2.png",height: 200,),
              SizedBox(height: 20,),
              Column(
                children: [
                  ElevatedButton(
                     onPressed: () => Navigator.of(context).pop(),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppColors.primary,
                      fixedSize: Size(300, 30),
                      foregroundColor: Colors.white,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                        
                      ),
                    ),
                    child: const Text("ok"),
                  ),

                 


                  
                ],
              ),
              










            ],
          ),
        ),
      ),
    );
  },
       child: Text('Pay Now'),
      style: ElevatedButton.styleFrom(
        backgroundColor: AppColors.primary,
        fixedSize: Size(150, 50),
        foregroundColor: AppColors.white
      ),
      )
        ],
      )),
    );
  }
}