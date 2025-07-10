import 'package:charity_project/view/pay_details_page.dart';
import 'package:flutter/material.dart';
import 'package:charity_project/app_colors.dart';

class DonateWidget extends StatefulWidget {
  const DonateWidget({super.key});

  @override
  State<DonateWidget> createState() => _DonateWidgetState();
}

class _DonateWidgetState extends State<DonateWidget> {
  final formKey = GlobalKey<FormState>();
  final List<int> amounts = [10, 20, 50, 100];
  int? selectedAmount;
  final TextEditingController amountController = TextEditingController();

  void updateAmount(int amount) {
    setState(() {
      selectedAmount = amount;
      amountController.text = amount.toString();
    });
  }

  void onTextChanged(String value) {
    final entered = int.tryParse(value);
    setState(() {
      selectedAmount = (entered != null && entered > 0 && entered < 1000) ? entered : null;
    });
  }

  bool get isValid => selectedAmount != null;

  @override
  Widget build(BuildContext context) {
    final displayAmount = selectedAmount ?? 0;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 10),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: amounts.map((amount) {
              final isSelected = selectedAmount == amount;
              return Expanded(
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 4),
                  child: GestureDetector(
                    onTap: () => updateAmount(amount),
                    child: Container(
                      height: 50,
                      decoration: BoxDecoration(
                        color: isSelected ? AppColors.primary : AppColors.white,
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

        // إدخال يدوي للمبلغ
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 10),
          child: Form(
            key: formKey,
            child: TextFormField(
              controller: amountController,
              keyboardType: TextInputType.number,
              onChanged: onTextChanged,
              cursorColor: AppColors.primary,
              validator: (value) {
                final val = int.tryParse(value ?? '') ?? 0;
                if (val > 1000) return "Amount must be less than 1000";
                if (val <= 0) return "Please enter a valid amount";
                return null;
              },
              decoration: InputDecoration(
                labelText: "Another amount",
                suffix: const Text("\$"),
                labelStyle: TextStyle(color: AppColors.primary),
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
        ),

        const SizedBox(height: 30),

        // عرض المبلغ الإجمالي
        Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Text(
              "Total Amount: ",
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            Text(
              isValid ? "$displayAmount \$" : "__",
              style: TextStyle(
                color: AppColors.primary,
                fontSize: 18,
                fontWeight: FontWeight.bold,
              ),
            )
          ],
        ),

        const SizedBox(height: 30),

        // زر الدفع
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 50),
          child: ElevatedButton(
            onPressed: isValid && formKey.currentState!.validate()
                ? () {
                    final amount = amountController.text;
                    // ScaffoldMessenger.of(context).showSnackBar(
                    //   SnackBar(content: Text("Paid $amount \$")),
                    // );
                    Navigator.push(context, MaterialPageRoute(builder: (context)=> PayDetailsPage()));
                  }
                : null,
            style: ElevatedButton.styleFrom(
              backgroundColor: AppColors.primary,
              foregroundColor: Colors.white,
              fixedSize: const Size(250, 50),
            ),
            child: const Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(Icons.payment, size: 24),
                SizedBox(width: 10),
                Text("Pay Now", style: TextStyle(fontSize: 16)),
              ],
            ),
          ),
        ),

        const SizedBox(height: 20),

        // زر الإضافة إلى السلة
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 50),
          child: ElevatedButton(
            onPressed: isValid && formKey.currentState!.validate()
                ? () {
                    final amount = amountController.text;
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(content: Text("Added $amount \$ to cart")),
                    );
                  }
                : null,
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.white,
              foregroundColor: AppColors.primary,
              fixedSize: const Size(250, 50),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(25),
                side: BorderSide(color: AppColors.primary, width: 2),
              ),
            ),
            child: const Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(Icons.shopping_cart, size: 24),
                SizedBox(width: 10),
                Text("Add to Cart", style: TextStyle(fontSize: 16)),
              ],
            ),
          ),
        ),
      ],
    );
  }
}
