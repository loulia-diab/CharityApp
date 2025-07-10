import 'package:charity_project/app_colors.dart';
import 'package:charity_project/view/app_text_style.dart';
import 'package:charity_project/view/background.dart';
import 'package:flutter/material.dart';

final formkey = GlobalKey<FormState>();

class ZakahPage extends StatefulWidget {
  ZakahPage({super.key});

  @override
  State<ZakahPage> createState() => _zakahPageState();
}

class _zakahPageState extends State<ZakahPage> {
  final List<int> amounts = [10, 20, 50, 100];
  int? selectedamount;
  TextEditingController amountin = TextEditingController();

  void updateAmount(int amount) {
    setState(() {
      selectedamount = amount;
      amountin.text = amount.toString();
    });
  }

  void onTextChanged(String value) {
    final entered = int.tryParse(value);
    setState(() {
      selectedamount = (entered != null && entered < 1000) ? entered : null;
    });
  }

  bool get isValid => selectedamount != null;

  @override
  Widget build(BuildContext context) {
    final displayamount = selectedamount ?? 0;
    return 
    Scaffold(
      body: BackgroundWrapper(
        child: Form(
          key: formkey,
          child: Stack(
            children: [
              CustomScrollView(
                slivers: [
                  SliverAppBar(
                    leading: IconButton(onPressed: (){
                      Navigator.pop(context);
                    }, icon: Icon(Icons.arrow_back,color: AppColors.white
                    ,)),
                    pinned: true,
                    floating: false,
                    backgroundColor: Color(0xff028174),
                    expandedHeight: 220,
                    flexibleSpace: FlexibleSpaceBar(
                      title: Padding(
                        padding: const EdgeInsets.only(left:10),
                        child: Text(
                          "Zakah",
                          style: TextStyle(
                              color: Colors.white,
                              fontSize: 17,
                              fontWeight: FontWeight.w400),
                        ),
                      ),
                      // centerTitle: true,
                      background: Stack(
                        fit: StackFit.expand,
                        children: [
                          Container(
                            decoration: BoxDecoration(
                              image: DecorationImage(
                                fit: BoxFit.cover,
                                image: AssetImage('assets/images/zakah.jpg'),
                              ),
                            ),
                          ),
                          Container(
                            decoration: BoxDecoration(
                              gradient: LinearGradient(
                                colors: [AppColors.primary, Colors.transparent],
                                begin: Alignment.bottomCenter,
                                end: Alignment.topCenter,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                  SliverToBoxAdapter(
                    child: Padding(
                      padding: const EdgeInsets.only(left: 20, right: 20),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          SizedBox(height: 20),
                          Text(
                            "Goodness is the most important of all virtues because, without goodness, all other virtues would not make much of sense, it is their primary cause and ultimate purpose. "
                            "Goodness is engrained in human nature; it is a fulfilling sense of satisfaction by doing good deeds towards our surroundings. "
                            "The religion of Islam emphasizes on the importance of doing good and calls it by the name Sadaqah (The word Sadaqah derives from the Arabic root word “Sidq” which translates to “Truth”). "
                            "Allah SWT says in the Holy Quran: '… And whoever volunteers good – then indeed, Allah is appreciative and Knowing.'",
                            style: TextStyle(
                                fontWeight: FontWeight.w500,
                                color: AppColors.black,
                                fontSize: 16),
                          ),
                          Divider(color: AppColors.primary),
                          Padding(
                            padding: const EdgeInsets.only(left: 20,right: 20,top: 10),
                            child: Text('Set Zakah amount', style: AppTextStyle.a),
                          ),
                          Padding(
                            padding:
                                const EdgeInsets.only(top: 20, left: 10, right: 10),
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
                                        height: 50,
                                        width: 70,
                                        padding:
                                            EdgeInsets.symmetric(vertical: 14),
                                        decoration: BoxDecoration(
                                          color: isSelected
                                              ? AppColors.primary
                                              : AppColors.white,
                                          border: Border.all(color: AppColors.primary),
                                          borderRadius: BorderRadius.circular(20),
                                        ),
                                        alignment: Alignment.center,
                                        child: Text(
                                          "$amount \$",
                                          style: TextStyle(
                                            color: isSelected
                                                ? Colors.white
                                                : AppColors.primary,
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
                            padding:
                                const EdgeInsets.only(top: 20, left: 10, right: 10),
                            child: TextFormField(
                              cursorColor: AppColors.primary,
                              controller: amountin,
                              keyboardType: TextInputType.number,
                              onChanged: onTextChanged,
                              validator: (value) {
                                int val = int.tryParse(value ?? '') ?? 0;
                                if (val > 1000) return "Amount must be less than 1000";
                                if (val <= 0) return "Please enter a valid amount";
                                return null;
                              },
                              decoration: InputDecoration(
                                labelStyle: TextStyle(color: AppColors.primary),
                                labelText: "anouther amount",
                                suffix: Text('\$'),
                                focusedBorder: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(20),
                                  borderSide: BorderSide(color: AppColors.primary),
                                ),
                                border: OutlineInputBorder(
                                  borderRadius: BorderRadius.circular(20),
                                ),
                              ),
                            ),
                          ),
                          SizedBox(height: 30),
                          Row(
                            children: [
                              Padding(
                                padding:
                                    const EdgeInsets.only(left: 20, right: 20),
                                child: Text(
                                  'Total Amount :',
                                  style: TextStyle(
                                    color: AppColors.black,
                                    fontSize: 20,
                                    fontWeight: FontWeight.w700,
                                  ),
                                ),
                              ),
                              Text(
                                isValid ? "$displayamount \$ " : "__",
                                style: TextStyle(
                                  color: AppColors.primary,
                                  fontSize: 20,
                                  fontWeight: FontWeight.w700,
                                ),
                              )
                            ],
                          ),
                          SizedBox(height: 40),
                          Column(
                            
                            
                            children: [
                              Padding(
                                padding: const EdgeInsets.symmetric(horizontal: 50),
                                child: ElevatedButton(
                                  onPressed: isValid
                                      ? () {
                                          if (formkey.currentState!.validate()) {
                                            final amount = amountin.text;
                                            ScaffoldMessenger.of(context)
                                                .showSnackBar(
                                              SnackBar(
                                                content:
                                                    Text("Paid $amount \$ "),
                                              ),
                                            );
                                          }
                                        }
                                      : null,
                                  child: Row(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      Icon(Icons.payment,
                                          color: AppColors.white, size: 30),
                                      SizedBox(width: 10),
                                      Text("Pay Now",
                                          style: TextStyle(fontSize: 16)),
                                    ],
                                  ),
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: AppColors.primary,
                                    foregroundColor: AppColors.white,
                                    fixedSize: Size(250, 50),
                                  ),
                                ),
                              ),
                              SizedBox(height: 20),
                              Padding(
                                padding: const EdgeInsets.only(bottom: 20),
                                child: ElevatedButton(
                                  onPressed: isValid
                                      ? () {
                                          if (formkey.currentState!.validate()) {
                                            final amount = amountin.text;
                                            ScaffoldMessenger.of(context)
                                                .showSnackBar(
                                              SnackBar(
                                                content: Text(
                                                    "Added $amount \$ to cart "),
                                              ),
                                            );
                                          }
                                        }
                                      : null,
                                  child: Row(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      Image.asset('assets/images/fund.png',
                                          color: AppColors.primary, height: 30),
                                      SizedBox(width: 10),
                                      Text("Add to Cart",
                                          style: TextStyle(fontSize: 16)),
                                    ],
                                  ),
                                  style: ElevatedButton.styleFrom(
                                    shape: RoundedRectangleBorder(
                                      side: BorderSide(
                                          color: AppColors.primary, width: 2),
                                      borderRadius: BorderRadius.circular(25),
                                    ),
                                    backgroundColor: AppColors.white,
                                    foregroundColor: AppColors.primary,
                                    fixedSize: Size(250, 50),
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ],
                      ),
                    ),
                  ),
                ],
              )
            ],
          ),
        ),
      ),
    );
  }
}

