from flask import Flask, request, jsonify
from gpt4all import GPT4All

app = Flask(__name__)

# Charger le modèle IA en local
model = GPT4All("gpt4all", model_path="C:/Users/emnam/gpt4all/")

@app.route("/chat", methods=["POST"])
def chat():
    data = request.json
    user_message = data["message"]

    # Générer une réponse IA
    response = model.generate(user_message)

    return jsonify({"response": response})

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000)

